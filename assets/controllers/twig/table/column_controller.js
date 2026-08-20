import AbstractController from "../../AbstractController.js";

export const FILTER = "App\\Component\\Twig\\Table\\Column_filter";
/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/

export default class extends AbstractController {
    static values = {
        queryName: String,
    }

    static targets = ["option"];

    connect() {
        this.optionTargets.forEach((link) => {
            link.addEventListener('click', (event) => this.filter(event, FILTER, this.queryNameValue, link.dataset.value));
        });

        this.processColumn();
    }

    processColumn() {
        let currentPath = new URL(document.location);
        if (currentPath.searchParams.has(this.queryNameValue)) {
            let columns = currentPath.searchParams.get(this.queryNameValue);
            columns = JSON.parse(decodeURIComponent(decodeURIComponent(columns)));
            if (columns instanceof Array) {
            } else {
                let newValue = columns;
                columns = [];
                columns.push(newValue);
            }
            let table = document.querySelector('table.table-sm')
            this.showHide(table, 'thead', columns);
            this.showHide(table, 'tbody', columns);
        }
    }

    showHide(table, type, columns){
        let trs = table.querySelector(type).children;
        for (let i = 0; i < trs.length; i++) {
            let cell = trs[i].children;
            for (let j = 0; j < cell.length; j++) {
                if (columns.indexOf(String(j)) !== -1) {
                    cell[j].style.display = 'none';
                }
            }
        }
    }

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
        if (currentPath.searchParams.has(queryName)) {
            var value = currentPath.searchParams.get(queryName);
            value = JSON.parse(decodeURIComponent(decodeURIComponent(value)));
            if (value instanceof Array) {
                if (value.indexOf(data) === -1) {
                    value.push(data);
                }else{
                    let position = value.indexOf(data);
                    value.splice(position,1);
                }
            } else {

                if (value.indexOf(data) === -1) {
                    let newValue = [];
                    newValue.push(value);
                    newValue.push(data);
                }else{
                    let position = value.indexOf(data);
                    value.splice(position,1);
                }
            }
        } else {
            value = [data];
        }

        if(value.length === 0){
            currentPath.searchParams.delete(queryName);
        }else{
            value = JSON.stringify(value);
            currentPath.searchParams.set(queryName, encodeURIComponent(value));
        }

        this.dispatch(dispatchEvent, {detail: {url: currentPath}});
    }

}
