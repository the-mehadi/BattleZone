# Debug Session: join-payment-failure

Status: OPEN

## Symptom
- On `http://127.0.0.1:8000/rooms/2/join`, submitting a valid join request shows:
  - `We could not complete your join request right now. Please try again.`

## Expected
- If the user has enough wallet balance, the squad should be created and the entry fee should be deducted.

## Reproduction
1. Login as a player with enough wallet balance.
2. Open `/rooms/2/join`.
3. Fill all required player slots.
4. Click `Join & Pay`.

## Falsifiable Hypotheses
1. The failure happens during `Squad::create()` and the insert is throwing a database exception.
2. The failure happens during `squadPlayers()->createMany()` because submitted player payload shape or DB constraints are invalid.
3. The failure happens inside `WalletService::deduct()` due to a DB write error, transaction nesting issue, or user row lookup issue.
4. The failure happens because the request passes the controller pre-check but fails later due to stale room/user state inside the transaction.
5. The failure happens because a database uniqueness/constraint rule is being hit and the controller is masking it as a generic error.

## Plan
1. Add instrumentation only around the join transaction path.
2. Reproduce the bug and collect runtime evidence.
3. Identify the exact failing statement from logs.
4. Apply the minimal fix based on evidence.
