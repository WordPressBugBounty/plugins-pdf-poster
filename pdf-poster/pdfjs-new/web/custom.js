document.addEventListener("DOMContentLoaded", function () {
  function parseURLParams(url) {
    var queryStart = url.indexOf("?") + 1,
      queryEnd = url.indexOf("#") + 1 || url.length + 1,
      query = url.slice(queryStart, queryEnd - 1),
      pairs = query.replace(/\+/g, " ").split("&"),
      parms = {},
      i,
      n,
      v,
      nv;

    if (query === url || query === "") return;

    for (i = 0; i < pairs.length; i++) {
      nv = pairs[i].split("=", 2);
      n = decodeURIComponent(nv[0]);
      v = decodeURIComponent(nv[1]);

      if (!parms.hasOwnProperty(n)) parms[n] = [];
      parms[n] = nv.length === 2 ? v : null;
    }
    return parms;
  }

  const pdfjsHistory = JSON.parse(
    window.localStorage.getItem("pdfjs.history")
  )?.files.find(
    (item) =>
      item.fingerprint === PDFViewerApplication?.store?.file?.fingerprint
  );

  const parseURL = parseURLParams(location.href);
  const openFile = document.getElementById("openFile");
  const sidebarToggle = document.getElementById("sidebarToggleButton");
  const print = document.getElementById("printButton");
  const download = document.getElementById("downloadButton");
  const secondaryOpenFile = document.getElementById("secondaryOpenFile");
  const secondaryPrint = document.getElementById("secondaryPrint");
  const secondaryDownload = document.getElementById("secondaryDownload");
  const viewerContainer = document.getElementById("viewerContainer");
  const outerContainer = document.getElementById("outerContainer");
  const toolbar = document.querySelector(".toolbar");
  const presentationMode = document.querySelectorAll(".presentationMode");
  const pdfViewer = document.querySelector(".pdfViewer");
  const scrollHorizontalButton = document.getElementById("scrollHorizontal");
  const scrollVerticalButton = document.getElementById("scrollVertical");
  const documentProperties = document.getElementById(
    "documentPropertiesDialog"
  );

  let css = "";
  if (parseURL?.raw) {
    css = `:root { --scrollbar-bg-color: transparent;--scrollbar-color: transparent; } body {background:transparent} .toolbar {display: none} .bottombar {display: none} .pdfViewer .page {border-image: url()} #viewerContainer{top:0} `;
    // pdfjsHistory.files[0].sidebarView = 0;
  }
  if (parseURL?.hrscroll) {
    css += ".bottombar{display: none;}";
  }
  const style = document.createElement("style");
  style.innerHTML = css;
  document.querySelector("head").appendChild(style);

  setInterval(() => {
    const canvases = document.querySelectorAll(".canvasWrapper canvas");
    canvases.forEach((canvas) => {
      canvas.toDataURL = () => console.warn("no cheating!");
      canvas.getContext = () => console.warn("no cheating!");
    });
  }, 3000);

  if (sidebarToggle) {
    // pdfjsHistory.files[0].sidebarView = parseURL?.sidebarOpen === "true" ? 1 : 0;
  }

  if (openFile && parseURL?.open) {
    openFile.style.display = "none";
  }

  if (parseURL?.stdono != "vera") {
    window.print = () => {
      console.warn("Print disabled!");
    };
    print?.parentNode.removeChild(print);
    secondaryPrint?.parentNode.removeChild(secondaryPrint);
  }

  if (download && parseURL?.nobaki != "vera") {
    window.addEventListener("selectstart", function (e) {
      e.preventDefault();
      console.warn("Content selection disabled!");
    });

    setTimeout(() => {
      documentProperties?.parentNode.removeChild(documentProperties);
    }, 1000);
    download?.parentNode.removeChild(download);
    secondaryDownload?.parentNode.removeChild(secondaryDownload);
  }

  if (secondaryOpenFile && parseURL?.open) {
    secondaryOpenFile.style.display = "none";
  }

  if (presentationMode && parseURL?.fullscreen != "1") {
    Object.values(presentationMode).map((item) => {
      item.style.display = "none";
    });
    // presentationMode.style.display = "none";
  }

  if (location.href.includes("blob:")) {
    download?.parentNode?.removeChild(download);
    secondaryDownload?.parentNode?.removeChild(secondaryDownload);
  }

  //sidebar toggle
  if (sidebarToggle && parseURL?.side != "true") {
    sidebarToggle.style.display = "none";
  }

  //raw css

  const interval = setInterval(() => {
    if (PDFViewerApplication.store?.fingerprint) {
      // PDF loaded - clear interval
      clearInterval(interval);

      // change scroll behavior
      setTimeout(() => {
        if (parseURL?.hrscroll === "vera") {
          PDFViewerApplication.appConfig.secondaryToolbar.scrollHorizontalButton.click();
        } else {
          PDFViewerApplication.appConfig.secondaryToolbar.scrollVerticalButton.click();
        }

        // update zoom level
        if (parseURL.z) {
          console.log(
            "currentscale",
            PDFViewerApplication.pdfViewer.currentScaleValue
          );
          PDFViewerApplication.pdfViewer.currentScaleValue = parseURL.z
            ? parseURL.z
            : "auto";
        }
      }, 100);
    }
  }, 100);

  const disableKey = (e) => {
    console.log("disabled");
    if (e.ctrlKey || e.shiftKey || e.altKey || e.key === "F12") {
      e.preventDefault();
      e.stopPropagation();
      return false;
    } else {
      return true;
    }
  };

  document.addEventListener("keydown", disableKey);
  window.addEventListener("keydown", disableKey);
  document.addEventListener("contextmenu", function (e) {
    e.preventDefault();
  });

  // window.localStorage.setItem('pdfjs.history', JSON.stringify(pdfjsHistory));
});
