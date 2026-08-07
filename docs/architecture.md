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

## Data boundary

`states` is the only persisted domain table in this version.

| Column | INEGI field | Constraint |
| --- | --- | --- |
| `state_code` | `cve_ent` | `char(2)`, unique; preserves leading zeroes |
| `name` | `nomgeo` | non-null |
| `total_population` | `pob_total` | unsigned integer, non-null |

Both INEGI endpoints return records in `datos`. Map only the fields above for states. For municipalities, return mapped `cve_ent`, `cve_mun`, `nomgeo`, and `pob_total`; do not persist the response or its metadata. If persistence is later needed, use the English plural `municipalities` table.

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
- Use an INEGI client service, HTTP timeouts, and mapped responses to isolate the untrusted upstream API.
- Use a unique index and upsert so imports are idempotent.
- Whitelist sort fields and validate pagination/search inputs.
- Keep MySQL private in deployment and secrets out of Git.
