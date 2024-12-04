declare const grecaptcha: any

const initForms = () => {
  const forms = document.querySelectorAll(
    ".ww-form",
  ) as NodeListOf<HTMLFormElement>

  forms.forEach((form: HTMLFormElement) => {
    form.addEventListener("submit", (event: SubmitEvent) => {
      event.preventDefault()

      const siteKey = form.dataset.sitekey ?? ""

      if (siteKey) {
        grecaptcha.ready(function () {
          grecaptcha.execute(siteKey, { action: "submit" }).then(function (
            token: string,
          ) {
            submitData(form, token)
          })
        })
      } else {
        submitData(form)
      }
    })
  })
}

initForms()

const submitData = async (
  form: HTMLFormElement,
  token: string | null = null,
) => {
  const button = form.querySelector("button[type=submit]") as HTMLButtonElement
  button.disabled = true
  const formData = new FormData()
  const submission = Object.fromEntries(new FormData(form))

  const fileFields = form.querySelectorAll(
    "input[type=file]",
  ) as NodeListOf<HTMLInputElement>
  fileFields.forEach((fileField) => {
    const files = fileField.files
    if (files) {
      formData.append(fileField.name, files[0])
    }
  })

  formData.append("action", "submit_form_data")
  formData.append("formId", form.dataset.id ?? "")
  formData.append("submission", JSON.stringify(submission))

  if (token) {
    formData.append("recaptchaToken", token)
  }

  const response = await fetch("/wp-admin/admin-ajax.php", {
    method: "post",
    body: formData,
  })

  const html = await response.text()

  const container = form.parentElement as HTMLElement
  container.innerHTML = html
  button.disabled = false
}
