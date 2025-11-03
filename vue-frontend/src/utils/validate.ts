import * as yup from 'yup'

export async function validate<T extends Record<string, any>>(
  schema: yup.ObjectSchema<T>,
  data: T
): Promise<{ valid: boolean; fieldErrors: Partial<Record<keyof T, string>> }> {
  try {
    await schema.validate(data, { abortEarly: false })
    return { valid: true, fieldErrors: {} }
  } catch (error) {
    const fieldErrors: Partial<Record<keyof T, string>> = {}
    if (error instanceof yup.ValidationError) {
      error.inner.forEach((e) => {
        if (e.path) fieldErrors[e.path as keyof T] = e.message
      })
    }
    return { valid: false, fieldErrors }
  }
}