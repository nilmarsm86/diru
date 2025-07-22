import {Controller} from "@hotwired/stimulus";
import {useContentLoader} from "./use-content-loader.js";

/**
 * Reload async content
 *
 * data-controller="content-loader"
 * data-content-loader-url-value="/path"
 * data-content-loader-event-loaded-name-value="event_name"
 * ?data-content-loader-load-on-connect-value
 *
 * ?data-content-loader-target="container"
 *
 * app.register('content-loader', ReloadContentController);
 */
/* stimulusFetch: 'lazy' */
export class ContentLoaderController extends Controller {

    /**
     * Values
     * @type {{
     *  loadOnConnect: {default: boolean, type: BooleanConstructor},
     *  eventLoadedName: {default: string, type: StringConstructor},
     *  url: {default: string, type: StringConstructor}
     * }}
     */
    static values = {
        url: {type: String, default: ''},
        loadOnConnect: {type: Boolean, default: false},
        eventLoadedName: {'type': String, 'default': 'loaded'},
    };

    /**
     * Targets
     * @type {string[]}
     */
    static targets = ['container'];

    /**
     * @inheritDoc
     */
    connect() {
        this.element['contentLoaderController'] = this;

        useContentLoader(this, {
            url: this.urlValue,
            container: (this.hasContainerTarget) ? this.containerTarget : this.element,
            eventLoadedName: this.eventLoadedNameValue,
        });

        this.loadOnConnect();
    }

    /**
     * Loaded content on connect
     */
    loadOnConnect() {
        if (this.loadOnConnectValue) {
            this.refreshContent(new CustomEvent('loadOnConnect'));
        }
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