/**
 * Creates a typewriter effect for paragraphs of text
 * @param {string[]} paragraphs - Array of text strings to type
 * @param {string} containerId - ID of the container element
 * @param {Object} options - Configuration options
 */
export default (paragraphs, containerId, options = {}) => {
    // Default options
    const config =
    {
        speed                   : options.speed || 15,
        startDelay              : options.startDelay || 250,
        paragraphDelay          : options.paragraphDelay || 150,
        showCursor              : options.showCursor !== undefined ? options.showCursor : true,
        showCompletionMessage   : options.showCompletionMessage !== undefined ? options.showCompletionMessage : true,
        completionMessage       : options.completionMessage || "[Letter continues...]"
    };

    if (config.showCursor && !document.getElementById('typewriter-cursor-style'))
    {
        const style = document.createElement('style');
        style.id = 'typewriter-cursor-style';
        style.textContent = `
            .typewriter-cursor {
                display: inline-block;
                width: 2px;
                height: 1em;
                background-color: #000;
                margin-left: 1px;
                animation: typewriter-blink 1s step-end infinite;
            }

            @keyframes typewriter-blink {
                from, to { opacity: 1; }
                50% { opacity: 0; }
            }
        `;
        document.head.appendChild(style);
    }

    const startTypewriter = () =>
    {
        let currentParaIndex = 0;
        let charIndex        = 0;
        const container = document.getElementById(containerId);

        if (!container)
        {
            console.error(`Container element with ID "${containerId}" not found`);

            return;
        }

        container.innerHTML = '';

        const paraElements = [];

        paragraphs.forEach((_, index) =>
        {
            const paragraph = document.createElement('p');
            paragraph.id = `${containerId}-para-${index}`;
            paragraph.style.visibility = 'visible';
            paragraph.style.opacity = '1';
            container.appendChild(paragraph);
            paraElements.push(paragraph);
        });

        let completionEl = null;

        if (config.showCompletionMessage)
        {
            completionEl = document.createElement('div');
            completionEl.className = 'typewriter-completion';
            completionEl.style.display = 'none';
            completionEl.textContent = config.completionMessage;
            completionEl.classList.add('text-zinc-400', 'dark:text-zinc-500');
            container.appendChild(completionEl);
        }

        function typeNextChar()
        {
            if (currentParaIndex >= paragraphs.length)
            {
                return;
            }

            const currentParaEl = paraElements[currentParaIndex];

            if (!currentParaEl)
            {
                console.error(`Paragraph element at index ${currentParaIndex} not found`);

                return;
            }

            const currentPara = paragraphs[currentParaIndex];

            if (charIndex >= currentPara.length)
            {
                currentParaEl.innerHTML = currentPara;

                currentParaIndex++;
                charIndex = 0;

                if (currentParaIndex < paragraphs.length)
                {
                    setTimeout(typeNextChar, config.paragraphDelay);
                }
                else
                {
                    if (completionEl)
                    {
                        completionEl.style.display = 'block';
                    }

                    const generatedElement = document.getElementById(`${containerId}-generated`);

                    if (generatedElement)
                    {
                        setTimeout(() => { generatedElement.style.display = 'flex'; }, 500);
                    }
                }

                return;
            }

            currentParaEl.innerHTML =
                currentPara.substring(0, charIndex + 1) +
                (config.showCursor ? '<span class="typewriter-cursor"></span>' : '');

            charIndex++;

            setTimeout(typeNextChar, config.speed);
        }

        setTimeout(typeNextChar, config.startDelay);
    };

    if (document.readyState === 'loading')
    {
        document.addEventListener('DOMContentLoaded', () =>
        {
            setTimeout(startTypewriter, 100);
        });
    }
    else
    {
        setTimeout(startTypewriter, 100);
    }
};
