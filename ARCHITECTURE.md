# Architecture

## Overview

This project follows a pragmatic DDD-style modular monolith structure on top of Laravel.

The main rule is:

`Http -> Action -> Services/Models -> Events -> Integration Listeners`

The codebase is organized by domain contexts:

- `Match`
- `Training`
- `Dictionary`
- `User`
- `Step`
- `Language`

Laravel infrastructure still exists, but business logic should be centered in `app/Domain`.

## Layers

### Http

Located in `app/Http`.

Responsibilities:

- accept HTTP requests
- validate input via Form Requests
- call one Action
- return API Resources / responses

Controllers should stay thin. They should not orchestrate multi-step business flows.

### Domain

Located in `app/Domain/<Context>`.

Responsibilities:

- business rules
- domain entities/models
- enums
- factories
- strategies
- actions
- domain services
- domain events

Each context should own its own logic as much as possible.

### Infrastructure

Located in `app/Infrastructure`.

Responsibilities:

- JWT/token helpers
- uploads/storage integrations
- Swagger/OpenAPI classes
- broker/external integration helpers

If something talks to the outside world or depends on framework/external services, it likely belongs here.

## Class Rules

### Action

`Action` is a use case.

Examples:

- create match
- start match
- submit match attempt
- skip match step

Use `Action` when a class coordinates several business steps and decides what happens next.

An `Action` may:

- call multiple Services
- update multiple models
- dispatch events
- decide whether a flow continues or stops

### Service

`Service` is a focused domain operation, not a full scenario.

Examples:

- create a step
- verify an attempt
- build a summary
- complete a model with a computed reason

Use `Service` when logic is reusable and local in scope.

If a class starts answering “what should happen next in the whole scenario?”, it probably wants to become an `Action`.

### Listener

`Listener` reacts to something that already happened.

Good listener responsibilities:

- publish to broker
- send notifications
- update projections/read models
- log or emit integration events

Bad listener responsibilities:

- decide core business flow
- complete the main use case
- generate the next step in the scenario
- mutate main domain state as the primary flow owner

If a listener is moving the business process forward, that logic should usually live in an `Action`.

### Model

Models hold state and small domain behaviors.

Good examples:

- transition own status
- expose relationships
- compute small entity-level behavior

Avoid putting multi-step orchestration across several entities into models.

### Factory / Strategy

Use factories when the app needs to choose an implementation based on runtime data.

Examples:

- completion condition factory
- match/training strategy factory
- step resolver / verifier factory

## Current Project Guidance

### Match

`Match` is the reference architecture for this project.

Core flow should live in `Actions`.
Listeners in `Match` should be integration-oriented only.

Examples of core flow:

- create/start match
- submit answer
- skip step
- decide participant completion
- decide match completion
- generate next step

Examples of integration flow:

- publish match summary
- publish match started/completed/created events

### Training

`Training` should move toward the same style as `Match`.

If future refactoring is needed, prefer introducing `Training` Actions instead of growing orchestration inside listeners.

## Folder Decisions

Use this checklist before creating a new class:

- “Is this an HTTP entrypoint?” -> `app/Http`
- “Is this a full business use case?” -> `app/Domain/<Context>/Actions`
- “Is this a reusable local business operation?” -> `app/Domain/<Context>/Services`
- “Is this a reaction/notification/integration side effect?” -> `app/Domain/<Context>/Listeners`
- “Is this external/framework integration?” -> `app/Infrastructure`

## Practical Rules

1. A controller should usually call one Action.
2. A use case should be readable from one class.
3. Services should stay narrower than Actions.
4. Listeners should not own the main business flow.
5. Events are allowed, but they should announce decisions, not hide them.
6. Keep public HTTP behavior backward-compatible unless explicitly changing the API.
7. Prefer adding logic inside the relevant domain context instead of generic global folders.

## When In Doubt

Ask one question:

“Is this class deciding the business flow, or reacting to it?”

- deciding the flow -> `Action`
- reacting to the flow -> `Listener`

