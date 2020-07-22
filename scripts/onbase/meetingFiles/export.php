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
define('EXPECTED_PATH', 'O:\DIP\onboard');

define('FORMAT_PDF', 16);

$table         = new MeetingFilesTable();
$committee     = new Committee(BDUAC);
$committeeName = $committee->getName();
$committeeDir  = EXPORT_DIR."/$committeeName";
$results       = $table->find(['committee_id' => $committee->getId()]);

if (!is_dir($committeeDir)) { mkdir($committeeDir, 0755, true); }
$index = fopen(EXPORT_DIR."/$committeeName.idx", 'w');

foreach ($results as $r) {
    $format   = FORMAT_PDF;
    $date     = $r->getMeetingDate('Y/m/d');
    $type     = $r->getType();
    $title    = $r->getTitle();
    $filename = $r->getFilename();
    $dir      = "$committeeDir/$date";
    $path     = str_replace('/', '\\', EXPECTED_PATH."/$committeeName/$date");
    $export   = "$dir/$filename";

    if (!is_dir($dir)) { mkdir($dir, 0775, true); }
    copy($r->getFullPath(), $export);

    fwrite($index, "------
TYPE:   $type
FORMAT: $format
PATH:   $path
FILE:   $filename
BOARD:  $committeeName
DATE:   $date
TITLE:  $title
");
    echo "{$r->getFullPath()} => $export\n";
}
