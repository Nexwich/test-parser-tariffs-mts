const elements = document.getElementsByClassName('js-submit');
const form = document.getElementById('form');

for (let i = 0; i < elements.length; i += 1) {
  elements[i].addEventListener('input', (event) => {
    form.submit();
  });
}



