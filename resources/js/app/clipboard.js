export default (userOptions) =>
{
    const options = {
        content: userOptions.content,
        onSuccess: userOptions.success ?? function () {},
        onError: userOptions.error ?? function () {},
    };

    if (userOptions.content === "")
    {
        return;
    }

    if (typeof userOptions.content === "function")
    {
        userOptions.content = userOptions.content();
    }

    let copyContent = 'Sorry, we had problems copying the content';

    try
    {
        const binaryString = atob(options.content);
        const bytes    = new Uint8Array(binaryString.length);

        for (let i = 0; i < binaryString.length; i++)
        {
            bytes[i] = binaryString.charCodeAt(i);
        }

        const decoder = new TextDecoder('utf-8');
        copyContent =  decoder.decode(bytes);
    }
    catch(e){}

    navigator.clipboard.writeText(copyContent).then(function ()
    {
        options.onSuccess();
    },
    function (err)
    {
        options.onError(err);
    });
};
