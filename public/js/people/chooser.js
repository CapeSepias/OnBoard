"use strict";
/**
 * Opens a popup window letting the user search for and choose a person
 *
 * To use this script the HTML elements must have the correct IDs so
 * we can update those elements when the callback is triggered.
 * You then register the PERSON_CHOOSER.open function as the onclick handler,
 * passing in the fieldname you are using for your inputs elements.
 *
 * Here is the minimal HTML required:
 * <input id="{$fieldId}" value="" />
 * <span  id="{$fieldId}-name"></span>
 * <a onclick="PERSON_CHOOSER.open('$fieldId');">Change Person</a>
 *
 * Example as it would appear in the final HTML:
 * <input id="reportedByPerson_id" value="" />
 * <span  id="reportedByPerson_id-name"></span>
 * <a onclick="PERSON_CHOOSER.open('reportedByPerson_id');">Change Person</a>
 *
 * @copyright 2013-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
var PERSON_CHOOSER = {
	fieldId: '',
	popup: {},
	open: function (e, fieldId) {
        e.preventDefault();
        e.stopPropagation();

		PERSON_CHOOSER.fieldId = fieldId;
		PERSON_CHOOSER.popup = window.open(
			ONBOARD.BASE_URL + '/people?popup=1;callback=PERSON_CHOOSER.setPerson',
			'popup',
			'menubar=no,location=no,status=no,toolbar=no,width=800,height=600,resizeable=yes,scrollbars=yes'
		);
        return false;
	},
	setPerson: function (person_id) {
        ONBOARD.ajax(
            ONBOARD.BASE_URL + '/people/view?format=json;person_id=' + person_id,
            function (request) {
                var id      = PERSON_CHOOSER.fieldId,
                    name    = PERSON_CHOOSER.fieldId + '-name',
                    person = JSON.parse(request.responseText);

                document.getElementById(id).value       = person.id;
                document.getElementById(name).innerHTML = person.fullname;
                PERSON_CHOOSER.popup.close();
            }
        );
    }
}
