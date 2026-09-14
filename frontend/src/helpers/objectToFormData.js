export function objectToFormData(obj, form = new FormData(), namespace = '') {
  for (let property in obj) {
    if (obj[property] === undefined || obj[property] === null) continue

    const key = namespace ? `${namespace}[${property}]` : property

    if (obj[property] instanceof File) {
      form.append(key, obj[property])
    } else if (typeof obj[property] === 'object' && !(obj[property] instanceof Date)) {
      objectToFormData(obj[property], form, key)
    } else {
      form.append(key, obj[property])
    }
  }

  return form
}
