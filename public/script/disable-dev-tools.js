/**
 * @author jimBoYz Ni ChOy!!!
 * This script handle disable developer tools and view-scource code when users inspect it
 */

(function() {
  // --- Disable right click ---
  document.addEventListener("contextmenu", (e) => e.preventDefault());

  // --- Block common DevTools shortcuts but allow Ctrl+K ---
  document.addEventListener("keydown", (e) => {
    // const key = e.key.toUpperCase();

    if (!e.key) return; // ✅ critical fix

    // e.key is a string (e.g. "a", "A", "F12", "Enter")
    // .toUpperCase() works for letters
    // Non-letter keys don’t need .toUpperCase()
    const key = e.key.length === 1 ? e.key.toUpperCase() : e.key;

    // F12
    if (key === "F12") e.preventDefault();

    // Ctrl + Shift + I/J/C
    if (e.ctrlKey && e.shiftKey && ["I", "J", "C"].includes(key)) e.preventDefault();

    // Ctrl + U (View Source)
    if (e.ctrlKey && key === "U") e.preventDefault();

    // Allow Ctrl + K (your shortcut)
    if (e.ctrlKey && key === "K") {
      e.preventDefault();
      // 🔹 Replace with your app's Ctrl+K function
      console.log("Ctrl + K triggered");
      // myCustomFunction();
    }
  });

  // --- Detect DevTools open ---

  // Safe for mobile
    //   const devtoolsDetector = () => {
    //     const threshold = 160; // difference in window 
        
    //     if (
    //       window.outerHeight - window.innerHeight > threshold ||
    //       window.outerWidth - window.innerWidth > threshold
    //     ) {
    //       document.body.innerHTML = "<h1 style='text-align:center;margin-top:20%;font-family:sans-serif;'>DevTools disabled 🚫</h1>";
    //     }
    //   };

    //   setInterval(devtoolsDetector, 1000);

  // Safe for mobile 100%
  const isMobile = /Mobi|Android|iPhone|iPad|iPod/i.test(navigator.userAgent);

  if (!isMobile) {
    const devtoolsDetector = () => {
    const threshold = 160;
    if (
      window.outerHeight - window.innerHeight > threshold ||
      window.outerWidth - window.innerWidth > threshold
    ) {
      document.body.innerHTML =
        "<h1 style='text-align:center;margin-top:20%;font-family:sans-serif;'>DevTools disabled 🚫</h1>";
    }
  };
  setInterval(devtoolsDetector, 1000);
}

})();

// setInterval(() => {
//   if (window.outerHeight - window.innerHeight > 200 ||
//       window.outerWidth - window.innerWidth > 200) {
//     document.body.innerHTML = "<h1>DevTools is disabled.</h1>";
//   }
// }, 1000);
