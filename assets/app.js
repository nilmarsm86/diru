import './bootstrap.js';

// import './js/color-modes.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap';
import Backdrop from "./components/backdrop.js";

import './styles/app.css';

document.getElementById('icons_only')?.addEventListener('click', function (e) {
    const sidebar = document.querySelector('.sidebar');
    sidebar.classList.toggle('col-lg-2');
    sidebar.classList.toggle('col-lg-1');

    const main = document.querySelector('main');
    main.classList.toggle('col-lg-10');
    main.classList.toggle('col-lg-11');
});
console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');
customElements.define("backdrop-component", Backdrop);//register un webcomponent
