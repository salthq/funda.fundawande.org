function animate(elem, style, unit, from, to, time, prop) {
  if (!elem) {
    return;
  }
  var start = new Date().getTime(),
    timer = setInterval(function() {
      var step = Math.min(1, (new Date().getTime() - start) / time);
      if (prop) {
        elem[style] = from + step * (to - from) + unit;
      } else {
        elem.style[style] = from + step * (to - from) + unit;
      }
      if (step === 1) {
        clearInterval(timer);
      }
    }, 25);
  if (prop) {
    elem[style] = from + unit;
  } else {
    elem.style[style] = from + unit;
  }
}

window.onload = function() {
  var target = document.getElementById("quiz-questions");
  if (target) {
    animate(
      document.scrollingElement || document.documentElement,
      "scrollTop",
      "",
      0,
      target.offsetTop - 350,
      1000,
      true
    );
  }
};
