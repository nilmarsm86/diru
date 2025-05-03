export const useFilter = (controller, options) => {
    Object.assign(controller, {

        /**
         * Show or hide backdrop
         * @param event
         * @param dispatchEvent
         * @param queryName
         * @param data
         */
        filter(event, dispatchEvent, queryName, data) {
            event.preventDefault();
            event.stopImmediatePropagation();

            let currentPath = new URL(document.location);
            currentPath.searchParams.set(queryName, data);
            this.dispatch(dispatchEvent, {detail:{url:currentPath}});
        }
    });
};