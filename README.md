# Test submission - Linear Interpolation Fee Calculator
This solution uses a docker to remove the need for system level dependencies. You'll need a computer with the `docker` service and command working.

## Instructions
1. `make build` to build the docker image.
2. `make run term=12 amount=2404` to run the calculation.
3. `make test` to run tests

## Thoughts / How I could improve the solution

 - The responsibility for validating an `Application` could be handled by a separate service, not by `FeeCalculators`.
 - Converting numerical inputs (floats) to integers may have made it easier to avoid issues with precision when performing calculations, especially as bcmath expects strings for inputs/outputs.
 - Perhaps I could have used a pre-built 'event bus' and conformed to a better known design pattern.

## Brief
[Test instructions](OBJECTIVE.md)
