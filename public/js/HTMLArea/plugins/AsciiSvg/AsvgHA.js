/*
*  This file contains additional functions that work with ASCIISvg.js
*  Mainly this file contains handlers for the GUI's version of script
*  and support for the popup control panels.
*  This file is included by ascii-svg.js (the HTMLArea plugin) in the
*  header of the editor iFrame.
*
*  Don't include this file in other page headers; use AsciiSvgAddon.js
*
*  (c) 2005 David Lippman
*/

//if drawPictures is put into the onload, this will hopefully preempt it
//Better yet, for the version of AsciiSVG.js in the plugin directory
//remove the drawPictures function and the onload loader
function drawPictures() {
		
}
//ShortScript format:
//xmin,xmax,ymin,ymax,xscl,yscl,labels,xgscl,ygscl,width,height plotcommands(see blow)
//plotcommands: type,eq1,eq2,startmaker,endmarker,xmin,xmax,color,strokewidth,strokedash
function parseShortScript(sscript,gw,gh) {
	if (sscript == null) {
		initialized = false;
		sscript = picture.sscr;
	}
	
	var sa= sscript.split(",");
	
	if (gw && gh) {
		sa[9] = gw;
		sa[10] = gh;
		sscript = sa.join(",");
		picture.sscr = sscript;
	}
	
	if (sa.length > 10) {
		commands = 'setBorder(5);';
		commands += 'width=' +sa[9] + '; height=' +sa[10] + ';';
		commands += 'initPicture(' + sa[0] +','+ sa[1] +','+ sa[2] +','+ sa[3] + ');';
		commands += 'axes(' + sa[4] +','+ sa[5] +','+ sa[6] +','+ sa[7] +','+ sa[8]+ ');';
				
		var inx = 11;
		var eqnlist = 'Graphs: ';
		
		while (sa.length > inx+9) {
		   commands += 'stroke="' + sa[inx+7] + '";';
		   commands += 'strokewidth="' + sa[inx+8] + '";'
		   if (sa[inx+9] != "")
			   commands += 'strokedasharray="' + sa[inx+9] + '";'
		   if (sa[inx]=="slope") {
			   eqnlist += "dy/dx="+sa[inx+1] + "; ";
			commands += 'slopefield("' + sa[inx+1] + '",' + sa[inx+2] + ',' + sa[inx+2] + ');'; 
		   } else {
			if (sa[inx]=="func") {
				eqnlist += "y="+sa[inx+1] + "; ";
				eqn = '"' + sa[inx+1] + '"';
			} else if (sa[inx] == "polar") {
				eqnlist += "r="+sa[inx+1] + "; ";
				eqn = '["cos(t)*(' + sa[inx+1] + ')","sin(t)*(' + sa[inx+1] + ')"]';
			} else if (sa[inx] == "param") {
				eqnlist += "[x,y]=["+sa[inx+1] + "," + sa[inx+2] + "]; ";
				eqn = '["' + sa[inx+1] + '","'+ sa[inx+2] + '"]';
			}
			
			
			if (typeof eval(sa[inx+5]) == "number") {
		//	if ((sa[inx+5]!='null')&&(sa[inx+5].length>0)) {
				//commands += 'myplot(' + eqn +',"' + sa[inx+3] +  '","' + sa[inx+4]+'",' + sa[inx+5] + ',' + sa[inx+6]  +');';
				commands += 'plot(' + eqn +',' + sa[inx+5] + ',' + sa[inx+6] +',null,null,' + sa[inx+3] +  ',' + sa[inx+4] +');';
			
			} else {
				commands += 'plot(' + eqn +',null,null,null,null,' + sa[inx+3] +  ',' + sa[inx+4]+');';
			}
		   }
		   inx += 10;
		}
		
		try {
			eval(commands);
		} catch (e) {alert("Graph not ready");}
		
		picture.setAttribute("alt",eqnlist);
		//FireFox seems to choke when there is both a "width" attribute
		//and a style attribute.  Since resize in the editor changes
		//the style attribute, we'll stick with that one.
		//picture.setAttribute("width", sa[9]);
		//picture.setAttribute("height", sa[9]);
		picture.style.width = sa[9] + "px";
		picture.style.height = sa[10] + "px";
		return commands;
	}
}

var popupwindow = '';
var lastgraphname = null;

//handles calls from inside the iFrame (from click on graphs)
function popup(graphname) {
	//var mywindow;
	if (!popupwindow.closed && popupwindow.location) {
		if (lastgraphname != graphname) {  //if popup open, but different graph
			if (isIE) {
				var alignm = document.getElementById(graphname).style.styleFloat;
			} else {
				var alignm = document.getElementById(graphname).style.cssFloat;
			}
			
			if (alignm == "none") {
				alignm = document.getElementById(graphname).style.verticalAlign;
			}
			popupwindow.getsscr(document.getElementById(graphname).getAttribute('sscr'),graphname,alignm);
			lastgraphname = graphname;
		}
	} else {  //popup is closed
		svgpluginpath = document.getElementById(graphname).getAttribute('src');
		svgpluginpath = svgpluginpath.substring(0,svgpluginpath.lastIndexOf("/"));
		popupwindow = window.open(svgpluginpath+"/svggraphcpwp.htm","mywindow","width=680,height=320,resizable=1,status=1");
		lastgraphname = graphname;
	}
	if (window.focus) {popupwindow.focus()}
	//setTimeout(function() { mywindow.getsscr(document.getElementById(graphname).getAttribute('sscr'),graphname);}, 300);
	
}

function setsscr() {
	if (isIE) {
		var alignm = document.getElementById(lastgraphname).style.styleFloat;
	} else {
		var alignm = document.getElementById(lastgraphname).style.cssFloat;
	}
	if (alignm == "none") {
		alignm = document.getElementById(lastgraphname).style.verticalAlign;
	}

	popupwindow.getsscr(document.getElementById(lastgraphname).getAttribute('sscr'),lastgraphname,alignm);
}

function defineGraph(text,graphname,alignment) {
	initialized = false;
	switchTo(graphname);
	parseShortScript(text);
	document.getElementById(graphname).setAttribute('sscr',text);
	if ((alignment == "left") || (alignment == "right")) {
		if (isIE) {
			document.getElementById(graphname).style.styleFloat = alignment;
		} else {
			document.getElementById(graphname).style.cssFloat = alignment;
		}
	} else {
		if (isIE) {
			document.getElementById(graphname).style.styleFloat = "none";
		} else {
			document.getElementById(graphname).style.cssFloat = "none";
		}
		document.getElementById(graphname).style.verticalAlign = alignment;
	}
}

function resizePic(evt) {
	pics = document.getElementsByTagName("embed");
	for (var i=0; i<pics.length; i++) {
		pic = pics[i];
		switchTo(pic.id);
		initialized = false;
		parseShortScript(pic.sscr,pic.style.width.replace(/(\d+).*/,"$1"),pic.style.height.replace(/(\d+).*/,"$1"));
	}
}



function drawPics() {
  var index, nd;
  pictures = document.getElementsByTagName("embed");
  var len = pictures.length;
  
 if (checkIfSVGavailable) {
  nd = isSVGavailable();
  if (nd != null && notifyIfNoSVG && len>0)
    if (alertIfNoSVG)
      alert("To view the SVG pictures in Internet Explorer\n\
download the free Adobe SVGviewer from www.adobe.com/svg or\n\
use Firefox 1.5 preview (called Deerpark)");
    else {
    var ASbody = document.getElementsByTagName("body")[0];
    ASbody.insertBefore(nd,ASbody.childNodes[0]);
  }
 }
 if (nd == null) {
  for (index = 0; index < len; index++) {
	  picture = pictures[index];
	  initialized = false;
	  var sscr = picture.getAttribute("sscr");
	  if ((sscr != null) && (sscr != "")) {
		  try {
                    if (isIE) {  //don't parseshortscript if FireFox
			  parseShortScript(sscr);
			  //names the SVG so it can identify itself onclick
			  //using instead of click_listener to get this out
			  //of AsciiSvg.js
			  picture.window.setname(picture.id);
                    }
		  } catch (e) {}
	  }
  }
 }
}

//modified by David Lippman
//added min/max type:  0:nothing, 1:arrow, 2:open dot, 3:closed dot
function plot(fun,x_min,x_max,points,id,min_type,max_type) {
  var pth = [];
  var f = function(x) { return x }, g = fun;
  var name = null;
  if (typeof fun=="string") 
    eval("g = function(x){ with(Math) return "+mathjs(fun)+" }");
  else if (typeof fun=="object") {
    eval("f = function(t){ with(Math) return "+mathjs(fun[0])+" }");
    eval("g = function(t){ with(Math) return "+mathjs(fun[1])+" }");
  }
  if (typeof x_min=="string") { name = x_min; x_min = xmin }
  else name = id;
  var min = (x_min==null?xmin:x_min);
  var max = (x_max==null?xmax:x_max);
  if (max <= min) { return null;}
  //else {
  var inc = max-min-0.000001*(max-min);
  inc = (points==null?inc/200:inc/points);
  var gt;
//alert(typeof g(min))
  for (var t = min; t <= max; t += inc) {
    gt = g(t);
    if (!(isNaN(gt)||Math.abs(gt)=="Infinity")) pth[pth.length] = [f(t), gt];
  }
  path(pth,name);
  if (min_type == 1) {
	arrowhead(pth[1],pth[0]);
  } else if (min_type == 2) {
	dot(pth[0], "open");
  } else if (min_type == 3) {
	dot(pth[0], "closed");
  }
  if (max_type == 1) {
	arrowhead(pth[pth.length-2],pth[pth.length-1]);
  } else if (max_type == 2) {
	dot(pth[pth.length-1], "open");
  } else if (max_type == 3) {
	dot(pth[pth.length-1], "closed");
  }

  return p;
  //}
}
