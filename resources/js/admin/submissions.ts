import { paginate } from "./paginate"
import { format, parseJSON } from "date-fns"

type FormField = {
  label: string
  name: string
  type: string
}

type FormSubmission = {
  id: string
  data: Record<string, string>
  submission_date: string
}

export const initSubmissions = async () => {
  const table = document.getElementById(
    "submissions-table",
  ) as HTMLTableElement | null

  if (table) {
    const container = document.querySelector(
      ".submissions-wrapper",
    )! as HTMLElement

    let limit = 10
    let offset = 0
    let sort = "desc"
    const toDelete = [] as string[]

    // initial data load
    const response = await getSubmissions(
      table.dataset.id!,
      limit,
      offset,
      sort,
      true,
    )

    let totalItems = parseInt(response.total)

    // render table parts
    renderHeadings(response.fields)
    renderTable(response.fields, response.results)
    renderPagination(limit, offset, totalItems)

    // per page event listener
    const perPage = document.getElementById(
      "per-page-selector",
    )! as HTMLSelectElement
    perPage.addEventListener("change", async () => {
      limit = parseInt(perPage.value)
      offset = 0

      const pageResponse = await getSubmissions(
        table.dataset.id!,
        limit,
        offset,
        sort,
      )
      renderTable(response.fields, pageResponse.results)

      renderPagination(limit, offset, totalItems)
    })

    // row checkbox event listener
    const tbody = document.querySelector("#submissions-table tbody")!
    tbody.addEventListener("click", (event: Event) => {
      const target = (event.target as HTMLElement).closest(
        'td.cell-checkbox input[type="checkbox"]',
      ) as HTMLInputElement | null

      if (target) {
        const row = target.closest("tr") as HTMLTableRowElement
        const id = row.dataset.id!

        if (target.checked) {
          toDelete.push(id)
        } else {
          const index = toDelete.indexOf(id)
          toDelete.splice(index, 1)
        }

        if (toDelete.length > 0) {
          renderPagination(limit, offset, totalItems, true)
        } else {
          renderPagination(limit, offset, totalItems)
        }
      }
    })

    // head checkbox event listener
    const thead = document.querySelector("#submissions-table thead tr")!
    thead.addEventListener("click", (event: Event) => {
      const target = (event.target as HTMLElement).closest(
        'td.check-column input[type="checkbox"]',
      ) as HTMLInputElement | null

      if (target) {
        const checkboxes = document.querySelectorAll(
          '#submissions-table tbody td.cell-checkbox input[type="checkbox"]',
        ) as NodeListOf<HTMLInputElement>

        if (target.checked) {
          checkboxes.forEach((checkbox) => {
            checkbox.checked = true
          })

          toDelete.splice(
            0,
            toDelete.length,
            ...Array.from(checkboxes).map(
              (checkbox) => checkbox.closest("tr")!.dataset.id!,
            ),
          )
          renderPagination(limit, offset, totalItems, true)
        } else {
          checkboxes.forEach((checkbox) => {
            checkbox.checked = false
          })

          toDelete.splice(0, toDelete.length)
          renderPagination(limit, offset, totalItems)
        }
      }
    })

    // delete button event listener
    container.addEventListener("click", async (event: Event) => {
      const target = (event.target as HTMLElement).closest(
        "button.delete-submissions",
      ) as HTMLButtonElement | null

      if (target) {
        const formData = new FormData()
        formData.append("action", "delete_submissions")
        formData.append("submissionIds", JSON.stringify(toDelete))

        const deleteResponse = await fetch("/wp-admin/admin-ajax.php", {
          method: "post",
          body: formData,
        })

        const results = await deleteResponse.text()
        const data = JSON.parse(results)

        if (data.success) {
          totalItems = totalItems - toDelete.length
          offset = 0
          toDelete.splice(0, toDelete.length)

          const pageResponse = await getSubmissions(
            table.dataset.id!,
            limit,
            offset,
            sort,
          )
          renderTable(response.fields, pageResponse.results)
          renderPagination(limit, offset, totalItems)
        }
      }
    })

    // sorting event listener
    const headers = document.querySelector("#submissions-table thead tr")!
    headers.addEventListener("click", async (event: Event) => {
      const target = (event.target as HTMLElement).closest(
        "th a",
      ) as HTMLAnchorElement | null

      if (target) {
        const parent = target.parentElement as HTMLElement

        sort = parent.classList.contains("asc") ? "desc" : "asc"
        offset = 0

        if (sort === "asc") {
          parent.classList.remove("desc")
          parent.classList.add("asc")
          parent.setAttribute("aria-sort", "ascending")
        } else {
          parent.classList.remove("asc")
          parent.classList.add("desc")
          parent.setAttribute("aria-sort", "descending")
        }

        const caption = document.querySelector("#submissions-table caption")!
        caption.textContent = `Table ordered by submission date. ${sort === "desc" ? "Descending" : "Ascending"}.`

        const pageResponse = await getSubmissions(
          table.dataset.id!,
          limit,
          offset,
          sort,
        )
        renderTable(response.fields, pageResponse.results)

        renderPagination(limit, offset, totalItems)
      }
    })

    // pagination event listener
    container.addEventListener("click", async (event: Event) => {
      let pages = (event.target as HTMLElement)?.closest(
        ".page-numbers",
      ) as HTMLElement | null

      let prev = (event.target as HTMLElement)?.closest(
        ".prev",
      ) as HTMLElement | null
      let next = (event.target as HTMLElement)?.closest(
        ".next",
      ) as HTMLElement | null

      if (pages) {
        if (pages.classList.contains("current")) {
          // do nothing on current page click
        } else {
          offset = (parseInt(pages.innerText) - 1) * limit

          const pageResponse = await getSubmissions(
            table.dataset.id!,
            limit,
            offset,
            sort,
          )
          renderTable(response.fields, pageResponse.results)
          renderPagination(limit, offset, totalItems)
        }
      } else if (prev) {
        offset = offset - limit

        const pageResponse = await getSubmissions(
          table.dataset.id!,
          limit,
          offset,
          sort,
        )
        renderTable(response.fields, pageResponse.results)
        renderPagination(limit, offset, totalItems)
      } else if (next) {
        offset = offset + limit

        const pageResponse = await getSubmissions(
          table.dataset.id!,
          limit,
          offset,
          sort,
        )
        renderTable(response.fields, pageResponse.results)
        renderPagination(limit, offset, totalItems)
      }
    })
  }
}

const getSubmissions = async (
  formId: string,
  limit: number = 10,
  offset: number = 0,
  sort = "desc",
  init = false,
) => {
  const formData = new FormData()
  formData.append("action", "load_submissions")
  formData.append("formId", formId)
  formData.append("limit", `${limit}`)
  formData.append("offset", `${offset}`)
  formData.append("sort", sort)
  formData.append("init", `${init}`)

  const response = await fetch("/wp-admin/admin-ajax.php", {
    method: "post",
    body: formData,
  })

  const results = await response.text()
  return JSON.parse(results)
}

const renderHeadings = (fields: FormField[]) => {
  const caption = document.querySelector("#submissions-table caption")!
  const thead = document.querySelector("#submissions-table thead tr")!

  caption.textContent = `Table ordered by submission date. Descending.`

  let headersHtml = `
    <td class="manage-column column-cb check-column">
      <input type="checkbox" id="cb-select-all-submissions" />
      <label for="cb-select-all-submissions"><span class="screen-reader-text">Select All</span></label>
    </td>
    <th scope="col" id="submission-date" class="manage-column column-date sorted desc" aria-sort="descending">
      <a>
        <span>Submitted</span>
        <span class="sorting-indicators">
          <span class="sorting-indicator asc" aria-hidden="true"></span>
          <span class="sorting-indicator desc" aria-hidden="true"></span>
        </span>
      </a>
    </th>
  `

  fields.forEach((field: FormField) => {
    if (field.type !== "heading") {
      headersHtml += `
        <th scope="col" class="manage-column">
          <span>${field.label}</span>
        </th>
      `
    }
  })

  thead.innerHTML = headersHtml
}

const renderTable = (fields: FormField[], submissions: FormSubmission[]) => {
  const tbody = document.querySelector("#submissions-table tbody")!
  let tbodyHtml = ""

  submissions.forEach((submission: FormSubmission) => {
    tbodyHtml += `<tr data-id="${submission.id}">`

    const submissionDate = parseJSON(submission.submission_date)

    tbodyHtml += `<td class="cell cell-checkbox"><input type="checkbox" /></td><td class="cell cell-small">${format(submissionDate, "Pp")}</td>`

    fields.forEach((field: FormField) => {
      if (field.type !== "heading") {
        if (field.type === "multiple_choice") {
          let answerValues = [] as string[]

          Object.keys(submission.data).forEach((key) => {
            if (key.includes(field.name)) {
              answerValues.push(submission.data[key])
            }
          })

          tbodyHtml += `<td class="cell cell-large">${answerValues.join(", ")}</td>`
        } else if (field.type === "checkbox") {
          const answer = submission.data[field.name] === "on" ? "true" : "false"
          tbodyHtml += `<td class="cell cell-large">${answer}</td>`
        } else {
          const answer = submission.data[field.name] ?? ""
          tbodyHtml += `<td class="cell cell-large">${answer}</td>`
        }
      }
    })

    tbodyHtml += "</tr>"
    tbody.innerHTML = tbodyHtml
  })
}

const renderPagination = (
  limit: number,
  offset: number,
  totalItems: number,
  deleteButton = false,
) => {
  const container = document.getElementById("submission-pagination")!

  const pagination = paginate(
    totalItems,
    Math.floor(offset / limit) + 1,
    limit,
    5,
  )

  let paginationHtml = ""

  if (deleteButton) {
    paginationHtml += `<button type="button" class="button delete-submissions">Move to trash</button>`
  }

  if (limit < totalItems) {
    paginationHtml += `<div class="pagination-container">`

    if (pagination.currentPage !== 1) {
      paginationHtml += `<button type="button" class="prev">Previous</button>`
    }

    pagination.pages.forEach((page) => {
      if (page === pagination.currentPage) {
        paginationHtml += `<span class="page-numbers current">${page}</span>`
      } else {
        paginationHtml += `<button type="button" class="page-numbers">${page}</button>`
      }
    })

    if (pagination.currentPage !== pagination.endPage) {
      paginationHtml += `<button type="button" class="next">Next</button>`
    }

    paginationHtml += `</div>`
  }

  container.innerHTML = paginationHtml
}
