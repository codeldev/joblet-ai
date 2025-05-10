import './app/bootstrap';

import clipboard from './app/clipboard';
import confetti from "canvas-confetti";
import typeWriter from "./app/typewriter";

window.showConfetti = function()
{
    setTimeout(confetti(
    {
        particleCount: 80,
        spread: 200,
    }), 1000);
}

window.typeWriter = typeWriter;

document.addEventListener('alpine:init', () =>
{
    Alpine.magic("clipboard", () => clipboard);
    Alpine.directive('typewriter', (el, {expression}, {evaluate}) =>
    {
        const [paragraphs, containerId, options] = evaluate(expression);
        typeWriter(paragraphs, containerId, options);
    });
});

Livewire.hook('request', ({ fail }) =>
{
    fail(({ status, preventDefault }) =>
     {
        if (status === 419)
        {
            preventDefault()
        }
    });
});
