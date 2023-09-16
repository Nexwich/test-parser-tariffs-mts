const forms = document.getElementsByClassName('form-reload');
console.log(forms);

for (let i = 0; i < forms.length; i += 1) {
  const form = forms[i];
  const button = form.getElementsByTagName('button')[0];
  const preloader = button.getElementsByTagName('i')[0];

  form.addEventListener('submit', async (event) => {
    event.preventDefault();

    button.setAttribute('disabled', 'disabled');
    preloader.classList.remove('hide');

    const formData = new FormData(form);

    const response = await fetch(form.action, {
      method: 'post',
      body: formData,
    });

    await response.json();
    location.reload();
  });
}
