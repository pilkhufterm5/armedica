// ----------------------------------------------
// StyleSwitcher functions written by Paul Sowden
// http://www.idontsmoke.co.uk/ss/
// - - - - - - - - - - - - - - - - - - - - - - -
// For the details, visit ALA:
// http://www.alistapart.com/stories/alternate/

function setActiveStyleSheet(title)
{
    var i, a, main;
    for(i=0; (a = document.getElementsByTagName("link")[i]); i++)
    {
	    if(a.getAttribute("rel").indexOf("style") != -1 && a.getAttribute("title"))
	    {
        	    a.disabled = true;
        	    if(a.getAttribute("title") == title) a.disabled = false;
	    }
    }
}