const SHOW_EVENT = 'backdrop:show';
const HIDE_EVENT = 'backdrop:hide';

export default class extends HTMLElement {

    constructor() {
        super();

        const shadow = this.attachShadow({ mode: "open" });
        shadow.innerHTML = this.setHtml();
        shadow.appendChild(this.setCss());
    }

    /**
     * Evento que se dispara para motrar el backdrop
     * @param event
     */
    show(event){
        this.style.display = 'flex';
    }

    /**
     * Evento que se dispara para ocultar el backdrop
     * @param event
     */
    hide(event){
        this.style.display = 'none';
    }

    /**
     * Cuando se registra el backdrop en el DOM
     */
    connectedCallback(){
        this.style.display = 'none';
        this.style.position = 'absolute';
        this.style.width = '100%';
        this.style.height = '100%';
        this.style.top = '0';
        this.style.left = '0';

        window.addEventListener(SHOW_EVENT, this.show.bind(this));
        window.addEventListener(HIDE_EVENT, this.hide.bind(this));
    }

    /**
     * Cuando se quita el backdrop del DOM
     */
    disconnectedCallback(){
        this.removeEventListener(SHOW_EVENT, this.show.bind(this));
        this.removeEventListener(HIDE_EVENT, this.hide.bind(this));
    }

    /**
     * Set css syles
     */
    setCss(){
        const style = document.createElement("style");
        style.textContent = `
        .backdrop{
            /*position: absolute;*/
            width: 100%;
            height: 100%;
            background: #000;
            opacity: 0.5;
            align-items: center !important;
            justify-content: center !important;
            display: flex !important;
        }
        
        .spinner-border {            
            border: 0.25em solid currentcolor;
            border-right-color: transparent;            
            display: inline-block;
            width: 2rem;
            height: 2rem;
            vertical-align: -0.125em;
            border-radius: 50%;
            -webkit-animation: 0.75s linear infinite spinner-border;
            animation: 0.75s linear infinite spinner-border;
        }
        
        .visually-hidden{
            position: absolute !important;
            width: 1px !important;
            height: 1px !important;
            padding: 0 !important;
            margin: -1px !important;
            overflow: hidden !important;
            clip: rect(0, 0, 0, 0) !important;
            white-space: nowrap !important;
            border: 0 !important;
        }
      `;

        return style;
    }

    setHtml(){
        return `<div class="backdrop">
        <div class="spinner-border" role="status"><!-- Personalizar el spinner -->
            <span class="visually-hidden">Cargando...</span><!-- Personalizar el texto de cargando -->
        </div>
    </div>`;
    }
}

export {SHOW_EVENT, HIDE_EVENT};