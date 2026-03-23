import { App } from "./app.js";

const { createElement, render } = window.wp.element;

const root = document.getElementById("momsy-content-builder-root");

if (root) {
  // The shell mounts only on the dedicated front-end builder page.
  render(createElement(App), root);
}
