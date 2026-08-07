# Architecture

## Context

```plantuml
@startuml
title Geostatistical Keys - System Context
actor "Recruiter" as recruiter
rectangle "Vue 3 + Bootstrap\nPublic web page" as ui
rectangle "Laravel 13" as app
rectangle "MySQL 8.4\nstates" as db
rectangle "INEGI Geo Catalog" as inegi

recruiter --> ui
ui --> app : HTTPS
app --> db : read / upsert states
app --> inegi : GET /mgee\nGET /mgem/{state_code}
@enduml
```

## Entity relationship diagram

`states` is the only persisted domain table in this version. This diagram is the schema authority.

```plantuml
@startuml
title States schema
entity states {
  * id : unsigned bigint <<PK>>
  --
  * state_code : char(2) <<UQ>>
  * name : varchar(120)
  * total_population : unsigned bigint
  --
  created_at : timestamp UTC
  updated_at : timestamp UTC
  deleted_at : timestamp UTC nullable
}
@enduml
```

Both INEGI endpoints return records in `datos`. Map `cve_ent`, `nomgeo`, and `pob_total` to the `states` fields. For municipalities, return mapped `cve_ent`, `cve_mun`, `nomgeo`, and `pob_total`; do not persist the response or its metadata. If persistence is later needed, use the English plural `municipalities` table.

All timestamps are stored and exposed as UTC. The Vue client converts them for display using the browser timezone.

## Sequences

```plantuml
@startuml
title Import states
actor "User" as actor
participant "Laravel backend" as command
participant "INEGI client" as client
participant "MySQL" as db
participant "INEGI" as inegi

actor -> command : import states
command -> client : fetch states
client -> inegi : GET /mgee
inegi --> client : datos[]
client --> command : mapped state records
command -> db : upsert by state_code
db --> command : completed
@enduml
```

```plantuml
@startuml
title View municipalities
actor User
participant "Vue UI" as ui
participant "Laravel" as app
participant "INEGI" as inegi

User -> ui : select state
ui -> app : GET municipalities/{state_code}
app -> inegi : GET /mgem/{state_code}
inegi --> app : datos[]
app --> ui : mapped municipalities
ui --> User : render list
@enduml
```

## Engineering decisions

- Keep one Laravel application and a single public Vue page.
- The geographic scope is Mexico only. INEGI classes and two-character state codes intentionally model that fixed scope; introduce countries, composite geographic keys, and provider abstractions only when country selection becomes a requirement.
- Use endpoint-specific readonly DTOs to validate and map consumed INEGI response shapes before services persist or return them; the DTO is the external-data contract.
- Keep INEGI HTTP calls in a single-purpose service with finite timeouts.
- Use a unique index and upsert so imports are idempotent.
- Whitelist sort fields and validate pagination/search inputs.
- Group public endpoints beneath the `/api/v1` prefix so future incompatible
  API changes have an explicit version boundary.
- Derive catalog totals from persisted states. Retrieve the INEGI source label
  on demand, map only its required metadata field, and never persist it; a
  source failure must not prevent the local total from being returned.
- Keep public read endpoints stateless and rate-limited. CSRF protection applies
  to browser requests that mutate state through cookie-based sessions, not to
  the public read-only states endpoint.
- Keep MySQL private in deployment and secrets out of Git.
