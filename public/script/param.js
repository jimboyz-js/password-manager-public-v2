/**
 * @author jimBoYz Ni ChOy!!!
 * @param name [Name or Key of the paramater]
 * @param defaultVal [The value of the param to get. The default value if not set is null]
 * @param name [paramater name]
 * @param value [parameter value]
 */

// Get the script tag for *this* script
// const currentScript = document.currentScript;

// Use URL API to parse its src
// const url = new URL(currentScript.src);

// Extract query params safely
const params = new URLSearchParams(window.location.search);

// Helper to get param with default
export function getParam(name, defaultVal = null) {
    return params.has(name) ? params.get(name) : defaultVal;
}


const params_ = new URLSearchParams(window.location.search);
export function setParam(name, value) {
    params_.set(name, value);
    return params_.toString();
}

export function updateQueryParam(key, value, reload = false) {
  const params = new URLSearchParams(window.location.search);
  params.set(key, value);
  const newUrl = `${window.location.pathname}?${params.toString()}`;
  if (reload) {
    window.location.search = params.toString(); // reloads
  } else {
    window.history.replaceState({}, '', newUrl); // no reload
  }
}

export function getParamFromScript(param, script=null) {
  // Find the current script element

  // Alternative Way: you can use ID or query-selector
  // const scriptEl = document.getElementById("authScript"); 

  const scriptEl = script ? document.querySelector(script) : document.currentScript;

  // Parse its src attribute
  const url = new URL(scriptEl.src);
  const params = new URLSearchParams(url.search);

  return params.get(param)
}
