
import Fragment, {loadFragment} from 'core/fragment';
import Templates from 'core/templates';
import Pending from 'core/pending';
import {prefetchStrings} from 'core/prefetch';
import {get_string as getString} from 'core/str';
import DynamicForm from 'core_form/dynamicform';
import {add as addToast} from 'core/toast';
import {markFormAsDirty} from 'core_form/changechecker';
import * as pageSelectors from 'local_custompage/local/selectors';
import $ from 'jquery';
import Notification from 'core/notification';


let pageId = 0;
let contextId = 0;

export const init = (id, contextid) => {

    pageId = id;
    contextId = contextid;

  // Lets get the form and add into proper container
    editDetailsCard(pageId, contextId);
};


const editDetailsCard = (pageid, contextid) => {
    const pendingPromise = new Pending('local_custompage/details:edit');

    // Load audience form with data for editing, then toggle visible controls in the card.
    const detailsForm = initDetailsCardForm();
    detailsForm.load({'id': pageid, 'needactionbuttons': 1})
        .then(() => {
            return pendingPromise.resolve();
        })
        .catch(Notification.exception);
};

const initDetailsCardForm = () => {
    const detailsFormContainer = document.querySelector(pageSelectors.regions.detailsFormContainer);
    const detailsForm = new DynamicForm(detailsFormContainer, '\\local_custompage\\form\\page');

    // After submitting the form, update the card instance and description properties.
    detailsForm.addEventListener(detailsForm.events.FORM_SUBMITTED, data => {

        return getString('details-saved', 'local_custompage')
            .then(addToast).then(() => {
              return window.location.reload();
            });
    });

    return detailsForm;
};

