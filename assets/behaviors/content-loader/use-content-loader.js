/**
 * Load content async
 * @param controller
 * @param options
 * @returns {*}
 *
 * useContentLoader(this, {
 *     url: this.urlValue,
 *     container: this.containerTarget || this.element,
 *     eventLoadedName: this.eventLoadedNameValue || "loaded",
 * });
 */
export const useContentLoader = (controller, options) => {
    Object.assign(controller, {

        /**
         * Remove all childs of the pased element
         * @param element
         */
        removeAllChilds(element){
            while (element.firstChild) {
                element.removeChild(element.firstChild);
            }
        },

        /**
         * Add response to element
         * @param response
         * @param target
         */
        addChildsNodes(response, target){
            const nodes = new DOMParser().parseFromString(response, 'text/html').body.childNodes;
            this.removeAllChilds(target);
            target.append(...nodes);
        },

        /**
         * Refresh content
         * @param event
         * @returns {Promise<void>}
         */
        async refreshContent(event){
            event.preventDefault();

            const container = (event.detail?.container) ? event.detail.container : options.container;
            container.style.opacity = "0.5";

            const url = (event.detail?.url) ? event.detail.url : options.url;
            const request = new Request(url, {
                headers: new Headers({
                    'X-Requested-With': 'XMLHttpRequest'
                })
            });
            const response = await fetch(request);
            const responseText = await response.text();
            this.addChildsNodes(responseText, container);
            container.style.opacity = "1";
            this.dispatch(options.eventLoadedName || "loaded", {detail:{container, responseText}});
        }
    });
};