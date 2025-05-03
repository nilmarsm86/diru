/**
 * Submit form async
 * @param controller
 * @param options
 * @returns {*}
 *
 * useSubmitFormAsync(this, {
 *     container: this.element
 *     eventSuccessName: this.eventSuccessNameValue || "success",
 *     eventFailureName: this.eventFailureNameValue || "failure"
 * });
 */
export const useSubmitFormAsync = (controller, options) => {
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
         * Submit form
         * @param event
         * @returns {Promise<void>}
         */
        async submit(event){
            event.preventDefault();

            this.dispatch("startSubmit");

            options.container.style.opacity = "0.5";
            const form = options.container.querySelector('form');
            if(!form instanceof HTMLFormElement){
                throw new Error('Inside the controller element must be a form element.');
            }

            const request = new Request(form.action, {
                headers: new Headers({
                    'X-Requested-With': 'XMLHttpRequest'
                })
            });

            const response = await fetch(request, {
                method: form.method,
                body: new URLSearchParams(new FormData(form)),
            });

            options.container.style.opacity = "1";
            (response.ok) ? this.success(form, response) : this.failure(form, response);
        },

        /**
         * Success send
         * @param form
         * @param response
         * @returns {Promise<void>}
         */
        async success(form, response){
            this.dispatch((options.eventSuccessName || "success"), {detail:{form, response}});
        },

        /**
         * Failure send
         * @param form
         * @param response
         * @returns {Promise<void>}
         */
        async failure(form, response){
            this.addChildsNodes(await response.text(), options.container);
            this.dispatch((options.eventFailureName || "failure"), {detail:{form, response}});
        }
    })
    ;
};