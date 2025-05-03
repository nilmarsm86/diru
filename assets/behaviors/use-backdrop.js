export const useBackdrop = (controller, options) => {
    Object.assign(controller, {

        /**
         * Show or hide backdrop
         * @param backdropElement
         * @param action
         */
        backdrop(backdropElement, action) {
            let ident = 'twig/backdrop/backdrop'.replaceAll('/', '--');
            const backdrop = this.application.getControllerForElementAndIdentifier(backdropElement, ident);
            backdrop.dispatch(action, {detail: {id: backdropElement.dataset.id}});
        }
    });
};