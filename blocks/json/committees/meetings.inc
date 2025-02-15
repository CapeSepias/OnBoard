<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Committee $htis->committee
 * @param array     $this->meetings
 * @param int       $this->year
 */
use Application\Models\MeetingFile;

use Web\Block;
use Web\Url;


$out = [];
foreach ($this->meetings as $date=>$meeting) {

    $out[$date]['eventId' ] = !empty($meeting['eventId' ]) ? $meeting['eventId' ] : '';
    $out[$date]['summary' ] = !empty($meeting['summary' ]) ? $meeting['summary' ] : '';
    $out[$date]['location'] = !empty($meeting['location']) ? $meeting['location'] : '';
    $out[$date]['start'   ] = !empty($meeting['start'   ]) ? $meeting['start'   ] : '';
    $out[$date]['end'     ] = !empty($meeting['end'     ]) ? $meeting['end'     ] : '';
    $out[$date]['htmlLink'] = !empty($meeting['htmlLink']) ? $meeting['htmlLink'] : '';

    foreach (MeetingFile::$types as $type) {
        if (!empty(  $meeting['files'][$type])) {
            foreach ($meeting['files'][$type] as  $file) {
                  $out[$date]['files'][$type][] = $file->getData();
            }
        }
    }
}
echo json_encode($out, JSON_PRETTY_PRINT);
