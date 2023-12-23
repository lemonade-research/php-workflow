# PHP Workflow Library

[![License](https://img.shields.io/badge/license-MIT-blue.svg)](https://opensource.org/licenses/MIT)

The PHP Workflow Library is a powerful tool for creating and using workflows in PHP applications. 

## Features



## Installation

Install the library using [Composer](https://getcomposer.org/):

```bash
composer require lemonade/workflow
```

## Getting Started

### Creating a Workflow

To create a new Workflow, you need to instantiate the `Workflow` class:

## Concepts

By creating a new workflow instance and executing it with the workflow engine, which is part of the manager,
you get a result object that contains the result of the workflow execution. Internally this is represented by a promise.

A workflow consists of a set of tasks that are executed in a specific order. Each task can have dependencies on other
tasks. The workflow engine will make sure that the tasks are executed in the correct order and that the dependencies are
met. The workflow engine will also make sure that the tasks are executed in parallel if possible. If a task fails, the
workflow engine will retry the task a number of times before it gives up and marks the workflow as failed.

The created workflow is a data object and can be executed multiple times. It will return an internal representation of
the steps, most likely tasks, in the workflow. This representation can be used to start, pause and resume the workflow
execution. Every step which is executed will be stored in the database. This allows you to resume the workflow from the
last successful step if the workflow fails. In addition, this is used for timers and signals. 

## Testing

### Integration Tests with Marble Diagrams

The workflow library uses marble diagrams to test the integration of the workflow engine with your tasks in a simulated
time. This allows you to test the execution of your tasks in a deterministic way. The workflow engine will execute the
workflow based on the given marble diagram and will emit events for each step in the workflow. 

#### Example

Let's say you have a workflow with three tasks. The first task is a simple task that will return a value. The second task
is a timer task that will wait for 30 minutes. The third task is a signal task that will wait for a signal
to be emitted. The following marble diagram shows the execution of this workflow: `a 30m b|`.

## Contributing

Contributions are welcome! Feel free to open issues or submit pull requests for bug fixes, enhancements, or new
features.

## License

This library is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

Thank you for using the PHP Workflow Library! If you have any questions or need further assistance, please don't
hesitate to get in touch. Happy working!
