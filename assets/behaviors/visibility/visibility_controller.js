import {Controller} from "@hotwired/stimulus";
import {useVisibility} from "./use-visibility.js";

/**
 * Show hide elements
 *
 * data-controller="visibility"
 * data-visibility-query-value=".css_class|html_tag|#id"
 * data-visibility-css-class-value="css_class"
 * data-visibility-event-show-name-value="event_name"
 * data-visibility-event-hide-name-value="event_name"
 * data-visibility-event-toggle-name-value="event_name"
 *
 * data-visibility-target="target"
 * data-visibility-target="target"
 *
 * app.register('visibility', VisibilityController);
 */
export class VisibilityController extends Controller {
    /**
     * Targets
     * @type {string[]}
     */
    static targets = ['target'];

    /**
     * Values
     * @type {{
     *  cssClass: {default: string, type: StringConstructor},
     *  eventShowName: {default: string, type: StringConstructor},
     *  query: {default: string, type: StringConstructor},
     *  eventHideName: {default: string, type: StringConstructor}
     *  eventToggleName: {default: string, type: StringConstructor}
     * }}
     */
    static values = {
        query: {'type': String, 'default': ''},
        cssClass: {'type': String, 'default': ''},
        eventShowName: {'type': String, 'default': 'show'},
        eventHideName: {'type': String, 'default': 'hide'},
        eventToggleName: {'type': String, 'default': 'toggle'},
    };

    /**
     * @inheritDoc
     */
    connect() {
        this.element['visibilityController'] = this;

        useVisibility(this, {
            targets: (this.hasTargetTarget) ? this.targetTargets : [this.element],
            query: this.queryValue,
            cssClass: this.cssClassValue,
            eventShowName: this.eventShowNameValue,
            eventHideName: this.eventHideNameValue,
            eventToggleName: this.eventToggleNameValue,
        });
    }

    /**
     * @inheritDoc
     */
    dispatch(eventName, options) {
        const event = super.dispatch(eventName, options);
        console.groupCollapsed(`Trigger ${event.type}`);
        console.log(event.detail);
        console.groupEnd();
        return event;
    }
}