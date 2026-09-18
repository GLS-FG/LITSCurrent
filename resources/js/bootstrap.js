import axios from 'axios';
import $ from 'jquery';
import tippy from 'tippy.js';
import Chart from 'chart.js/auto';
import { decode } from "@googlemaps/polyline-codec";

window.$ = window.jQuery = $;
window.axios = axios;
window.tippy = tippy;
window.Chart = Chart;
window.googleMapsDecode = decode;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.tippy('[data-tippy-content]');
