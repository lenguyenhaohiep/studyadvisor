function initEditor(tagId) {
  var editor = new HTMLArea(tagId);
  var line = editor.config.toolbar[1] ? 1 : 0;
  editor.config.toolbar[line].push("separator","insertnewmath",
                            "insertmath","swapmathmode");
  editor.config.toolbar[line].push("separator","insertsvg");
  editor.registerPlugin(AsciiMath);
  editor.registerPlugin(AsciiSvg);
  editor.config.hideSomeButtons(" popupeditor lefttoright righttoleft ");
  //surrounds AsciiMath in red box while editting.  Change to your liking
  editor.config.pageStyle = "span.AMedit {border:solid 1px #ff0000}";
  editor.generate();
  return editor;
};
