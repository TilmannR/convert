'use strict'

// const { extendDefaultPlugins } = require('svgo')

module.exports = {
  multipass: true,
  js2svg: {
    pretty: true,
    indent: 1
  },
  plugins: ([
    {
      name: 'removeDimensions',
      active: true
    }
  ])
}
