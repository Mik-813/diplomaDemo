export const functionMultilineArgs = {
  'function-multiline-arguments': {
    meta: {
      type: 'layout',
      fixable: 'code',
      schema: [],
    },
    create: context => ({
      CallExpression: node => {
        if (node.arguments.length <= 1) return
        let startLine = node.callee.loc.end.line
        let endLine = node.loc.end.line
        if (startLine === endLine) return
        let argLines = node.arguments
          .flatMap(
            node => node.loc.start.line === node.loc.end.line
              ? [node.loc.start.line]
              : [node.loc.start.line, node.loc.end.line],
          )
        let endsInFunction = !!node.arguments[node.arguments.length - 1].body
        if (new Set(argLines.slice(0, -1)).size === 1 && endsInFunction) return
        argLines.push(startLine)
        argLines.push(endLine)
        if (new Set(argLines).size === argLines.length) return
        context.report({
          node,
          message: 'Enforce line breaks for multi-line function arguments',
          fix(fixer) {
            let actions = []
            node.arguments.forEach(
              (arg, i, arr) => {
                let prev = i === 0 ? startLine : arr[i - 1].loc.end.line
                if (arg.loc.start.line === prev) {
                  actions.push(fixer.insertTextBefore(arg, '\n'))
                }
              },
            )
            let lastArg = node.arguments[node.arguments.length - 1]
            if (lastArg.loc.start.line === endLine) {
              actions.push(fixer.insertTextAfter(lastArg, '\n'))
            }
            return actions
          },
        })
      },
    }),
  },
}
