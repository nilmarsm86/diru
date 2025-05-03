/**
 * Show hide elements
 * @param controller
 * @param options
 * @returns {*}
 *
 * useVisibility(this, {
 *     targets: this.targetTargets || [this.element],
 *     query: this.queryValue || '',
 *     cssClass: this.cssClassValue || '',
 *     eventShowName: this.eventShowNameValue || "show",
 *     eventHideName: this.eventHideNameValue || "hide",
 *     eventToggleName: this.eventToggleNameValue || "toggle",
 * });
 */
export const useVisibility = (controller, options) => {
    Object.assign(controller, {

        /**
         * Show/hide element
         * @param event
         */
        toggle(event) {
            event.preventDefault();

            const targets = this.findTarget(options);
            targets.forEach((target) => {
                (!options.cssClass)
                    ? (target.style.display = (target.style.display === "none") ? "block" : "none")
                    : target.classList.toggle(options.cssClass)
                ;
            });
            this.dispatch((options.eventToggleName || "toggle"), {detail: targets});
        },

        /**
         * Show element
         * @param event
         */
        show(event) {
            event.preventDefault();

            const targets = this.findTarget(options);
            targets.forEach((target) => {
                (!options.cssClass) ? target.style.display = "block" : target.classList.add(options.cssClass);
            });
            this.dispatch((options.eventShowName || "show"), {detail: targets});
        },

        /**
         * Hide element
         * @param event
         */
        hide(event) {
            event.preventDefault();

            const targets = this.findTarget(options);
            targets.forEach((target) => {
                (!options.cssClass) ? (target.style.display = "none") : target.classList.remove(options.cssClass);
            });
            this.dispatch((options.eventHideName || "hide"), {detail: targets});
        },

        /**
         * Find target
         * @param options
         * @returns {Element | any}
         */
        findTarget(options) {
            if(!options.query){
                return options.targets;
            }

            let targets = options.targets[0]?.querySelectorAll(options.query);
            if (targets.length === 0) {
                targets = document.querySelectorAll(options.query);
            }
            return targets;
        }
    });
};