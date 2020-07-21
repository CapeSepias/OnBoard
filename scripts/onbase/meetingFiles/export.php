<?php
/**
 * @copyright 2020 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);

use Application\Models\Committee;
use Application\Models\MeetingFilesTable;

include '../../../bootstrap.php';

define('BDUAC', 10);
define('EXPORT_DIR', SITE_HOME.'/export');

if (!is_dir(EXPORT_DIR)) { mkdir(EXPORT_DIR, 0755, true); }

$table         = new MeetingFilesTable();
$committee     = new Committee(BDUAC);
$committeeName = $committee->getName();
$results       = $table->find(['committee_id' => $committee->getId()]);
$index         = fopen(EXPORT_DIR."/$committeeName.idx", 'w');
foreach ($results as $r) {
    $date     = $r->getMeetingDate('Y/m/d');
    $type     = $r->getType();
    $title    = $r->getTitle();
    $filename = $r->getFilename();
    $dir      = EXPORT_DIR."/$committeeName/$date";
    $export   = "$dir/$filename";

    if (!is_dir($dir)) { mkdir($dir, 0775, true); }
    copy($r->getFullPath(), $export);

    fwrite($index, "------
TYPE:   $type
FORMAT: PDF
PATH:   $committeeName/$date
FILE:   $filename
BOARD:  $committeeName
DATE:   $date
TITLE:  $title
");
    echo "{$r->getFullPath()} => $export\n";
}
