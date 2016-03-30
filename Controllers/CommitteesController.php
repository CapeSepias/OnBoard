<?php
/**
 * @copyright 2014-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Controllers;

use Application\Models\Committee;
use Application\Models\CommitteeTable;
use Application\Models\Seat;
use Application\Models\SeatTable;
use Application\Models\VoteTable;
use Blossom\Classes\Controller;
use Blossom\Classes\Block;
use Blossom\Classes\Url;

class CommitteesController extends Controller
{
    private function loadCommittee($id)
    {
        try {
            return new Committee($id);
        }
        catch (\Exception $e) {
            $_SESSION['errorMessages'][] = $e;
            header('Location: '.BASE_URL.'/committees');
            exit();
        }
    }

    public function index()
    {
        $currentCommittees = Committee::data(['current' => true]);
        if ($this->template->outputFormat === 'html') {
            $this->template->blocks[] = new Block('committees/breadcrumbs.inc');
        }
        $this->template->title = $this->template->_(['committee', 'committees', count($currentCommittees)]);

        $table = new CommitteeTable();
        $this->template->blocks[] = new Block('committees/current.inc', ['data'=>$currentCommittees]);

        $pastCommittees = $table->find(['current'=>false]);
        if (count($pastCommittees)) {
            $this->template->blocks[] = new Block('committees/past.inc',    ['committees'=>$pastCommittees]);
        }
    }

    public function info()
    {
        $committee = $this->loadCommittee($_GET['committee_id']);
        if ($this->template->outputFormat === 'html') {
            $this->template->title = $committee->getName();
            $this->template->blocks[] = new Block('committees/breadcrumbs.inc', ['committee' => $committee]);
            $this->template->blocks[] = new Block('committees/header.inc',      ['committee' => $committee]);
            $this->template->blocks[] = new Block('committees/info.inc',        ['committee' => $committee]);
            $this->template->blocks[] = new Block('committeeStatutes/list.inc', [
                'statutes'  => $committee->getStatutes(),
                'committee' => $committee
            ]);
            $this->template->blocks[] = new Block('departments/list.inc', [
                'departments'    => $committee->getDepartments(),
                'disableButtons' => true
            ]);
            $this->template->blocks[] = new Block('committees/liaisons.inc',    ['committee' => $committee]);
        }
        else {
            $this->template->blocks[] = new Block('committees/info.inc',        ['committee' => $committee]);
        }
    }

    public function members()
    {
        $committee = $this->loadCommittee($_GET['committee_id']);
        if ($this->template->outputFormat === 'html') {
            $this->template->title = $committee->getName();
            $this->template->blocks[] = new Block('committees/breadcrumbs.inc', ['committee' => $committee]);
            $this->template->blocks[] = new Block('committees/header.inc',      ['committee' => $committee]);
        }
        if ($committee->getType() === 'seated') {
            $data = SeatTable::currentData(['committee_id'=>$committee->getId()]);
            $this->template->blocks[] = new Block('seats/data.inc', [
                'data'      => $data,
                'committee' => $committee,
                'title'     => $this->template->_(['current_member', 'current_members', count($data['results'])])
            ]);
        }
        else {
            $members = $committee->getMembers();
            $this->template->blocks[] = new Block('committees/partials/openMembers.inc', [
                'committee' => $committee,
                'members'   => $members,
                'title'     => $this->template->_(['current_member', 'current_members', count($members)])
            ]);
        }

    }

    public function update()
    {
        $committee =        !empty($_REQUEST['committee_id'])
            ? $this->loadCommittee($_REQUEST['committee_id'])
            : new Committee();

        if (isset($_POST['name'])) {
            try {
                $committee->handleUpdate($_POST);
                $committee->save();

                $url = BASE_URL."/committees/info?committee_id={$committee->getId()}";
                header("Location: $url");
                exit();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }

        $this->template->blocks[] = new Block('committees/breadcrumbs.inc', ['committee' => $committee]);
        $this->template->blocks[] = new Block('committees/header.inc',      ['committee' => $committee]);
        $this->template->blocks[] = new Block('committees/updateForm.inc',  ['committee' => $committee]);
    }

    public function end()
    {
        $committee =        !empty($_REQUEST['committee_id'])
            ? $this->loadCommittee($_REQUEST['committee_id'])
            : new Committee();

        if (isset($_POST['endDate'])) {
            try {
                $committee->saveEndDate($_POST['endDate']);

                $url = BASE_URL."/committees/info?committee_id={$committee->getId()}";
                header("Location: $url");
                exit();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }
        $this->template->blocks[] = new Block('committees/breadcrumbs.inc', ['committee' => $committee]);
        $this->template->blocks[] = new Block('committees/header.inc',      ['committee' => $committee]);
        $this->template->blocks[] = new Block('committees/endDateForm.inc', ['committee' => $committee]);
    }

    public function seats()
    {
        $committee = $this->loadCommittee($_GET['committee_id']);
        if ($this->template->outputFormat === 'html') {
            $this->template->title = $committee->getName();
            $this->template->blocks[] = new Block('committees/breadcrumbs.inc', ['committee' => $committee]);
            $this->template->blocks[] = new Block('committees/header.inc',      ['committee' => $committee]);
        }

        $block = new block('seats/list.inc', [
            'seats'     => $committee->getSeats($_GET),
            'committee' => $committee
        ]);
        if (isset($_GET['current'])) {
            $block->title = $_GET['current'] ? $this->template->_('seats_current') : $this->template->_('seats_past');
        }
        $this->template->blocks[] = $block;
    }

    public function applications()
    {
        $committee = $this->loadCommittee($_GET['committee_id']);

        $this->template->title = $committee->getName();
        $this->template->blocks[] = new Block('committees/breadcrumbs.inc',  ['committee' => $committee]);
        $this->template->blocks[] = new Block('committees/header.inc',       ['committee' => $committee]);
        $this->template->blocks[] = new Block('applications/reportForm.inc', ['committee' => $committee]);
        $this->template->blocks[] = new Block('applications/list.inc', [
            'committee'    => $committee,
            'applications' => $committee->getApplications(['archived'=>time()]),
            'title'        => $this->template->_('applications_archived'),
            'type'         => 'archived'
        ]);
    }
}
