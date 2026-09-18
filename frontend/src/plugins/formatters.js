import * as formatters from '../utils/formatters'

export default {
  install(app) {
    Object.entries(formatters).forEach(([name, formatter]) => {
      if (typeof formatter === 'function') {
        app.config.globalProperties[`$${name}`] = formatter
      }
    })
  },
}
