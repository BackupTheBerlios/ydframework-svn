<?xml version="1.0"?>
<?xml-stylesheet href="chrome://global/skin/" type="text/css"?>
<window
    id="findfile-window"
    title="Find Files"
    orient="horizontal"
    xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul">
    {if $test}
        <button id="find-button" label="Find"/>
    {/if}
    <button id="cancel-button" label="Cancel"/>
    {$otherbutton}
</window>