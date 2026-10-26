import {Modal} from "bootstrap";
import AbstractController from "../../AbstractController.js";
import {useBackdrop} from "../../../behaviors/use-backdrop.js";
import {HIDE as BACKDROP_HIDE, SHOW as BACKDROP_SHOW} from "../backdrop/backdrop_controller.js";
// import {pdfjsLib} from "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js";

/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="hello" attribute will cause
 * this controller to be executed. The name "hello" comes from the filename:
 * hello_controller.js -> "hello"
 *
 * Delete this file or adapt it for your use!
 */

export default class extends AbstractController {
    modal = null;

    static targets = ["backdrop"];

    connect() {
        useBackdrop(this);

        this.element.addEventListener('live-form:submit', this.submitForm.bind(this));
        this.element.addEventListener('live-form:submitEnd', this.submitEndForm.bind(this));

        this.element.addEventListener('live--land-form:submit', this.submitForm.bind(this));
        this.element.addEventListener('live--land-form:submitEnd', this.submitEndForm.bind(this));

        this.element.addEventListener('live--local-form:submit', this.submitForm.bind(this));
        this.element.addEventListener('live--local-form:submitEnd', this.submitEndForm.bind(this));
    }

    initialize() {
        this.modal = Modal.getOrCreateInstance(this.element);
        this.element.addEventListener('modal_close', (event) => {
            this.modal.hide();
            //     // this.modal.dispose();
            //     this.element.querySelector('button[class=btn-close]').click();
        });
        // // pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
        //     this.element.addEventListener('shown.bs.modal', async function (event) {
        //         let pdfUrl = "https://127.0.0.1:8000/province/print";
        //         const canvas = event.target.querySelector('#pdf-canvas');
        //         console.log(canvas);
        //         const context = canvas.getContext('2d');
        //         // const pdfUrl = "{{ path('app_generar_pdf') }}"; // Tu ruta de Symfony
        //
        //         try {
        //             // 1. Cargar el documento PDF
        //             const loadingTask = pdfjsLib.getDocument(pdfUrl);
        //             const pdf = await loadingTask.promise;
        //
        //             // 2. Obtener la primera página (puedes iterar para mostrar todas)
        //             const page = await pdf.getPage(1);
        //
        //             // 3. Definir la escala de visualización (ajústala según necesites)
        //             const scale = 1.5;
        //             const viewport = page.getViewport({ scale: scale });
        //
        //             // 4. Ajustar el tamaño del canvas al del PDF
        //             canvas.height = viewport.height;
        //             canvas.width = viewport.width;
        //
        //             // 5. Renderizar la página en el canvas
        //             const renderContext = {
        //                 canvasContext: context,
        //                 viewport: viewport
        //             };
        //             await page.render(renderContext).promise;
        //
        //         } catch (error) {
        //             console.error("Error al cargar o renderizar el PDF:", error);
        //             // Aquí podrías mostrar un mensaje de error en el modal
        //         }
        //     });

    }

    // close() {
    //     this.element.querySelector('button[class=btn-close]').click();
    // }

    submitForm(event){
        const selector = `form[name='${event.detail.form.name}']`;
        if(this.element.querySelector(selector)){
            this.backdrop(this.backdropTarget, BACKDROP_SHOW);
        }
    }

    submitEndForm(event){
        const selector = `form[name='${event.detail.form.name}']`;
        if(this.element.querySelector(selector)){
            this.backdrop(this.backdropTarget, BACKDROP_HIDE);
        }
    }
}
