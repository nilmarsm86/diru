import {Controller} from "@hotwired/stimulus";
import {useSubmitFormAsync} from "./use-submit-form-async.js";

/**
 * Submit form async
 *
 * data-controller="submit-form-async"
 * data-submit-form-async-event-success-name-value="event_name"
 * data-submit-form-async-event-failure-name-value="event_name"
 *
 * app.register('submit-form-async', SendAsyncFormController);
 */
export class SendAsyncFormController extends Controller {

    /**
     * Static values
     *
     * @type {{
     *  eventFailureName: {default: string, type: StringConstructor},
     *  eventSuccessName: {default: string, type: StringConstructor}
     * }}
     */
    static values = {
        eventSuccessName: {type: String, default: 'success'},
        eventFailureName: {type: String, default: 'failure'}
    };

    /**
     * @inheritDoc
     */
    connect() {
        this.element['submitFormAsyncController'] = this;

        useSubmitFormAsync(this, {
            eventSuccessName: this.eventSuccessNameValue,
            eventFailureName: this.eventFailureNameValue,
            container: this.element
        });
    }
}