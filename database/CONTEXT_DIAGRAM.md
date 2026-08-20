# System Context Diagram (DFD Level 0)

```mermaid
flowchart LR
    CUSTOMER([Customer]) -->|"1. submits feedback, places order, views menu"| CFMS
    CFMS -->|"2. feedback status & ticket number"| CUSTOMER
    CUSTOMER -->|"3. tracks feedback status"| CFMS

    ADMIN([Admin / Staff]) -->|"4. logs in, manages feedback, users, reports, settings"| CFMS
    CFMS -->|"5 dashboard, analytics, notifications"| ADMIN

    SMS_GW([SMS Gateway (EgoSMS)]) -->|"6. delivery receipts"| CFMS
    CFMS -->|"7. automated SMS acknowledgements"| SMS_GW

    GROQ([Groq AI (LLM assistant)]) -->|"8. AI-suggested responses"| CFMS
    CFMS -->|"9. feedback context for automation"| GROQ

    PAYMENT([Payment Providers (MTN MoMo / Airtel Money)]) -->|"10. payment confirmations"| CFMS
    CFMS -->|"11. payment requests for online orders"| PAYMENT

    CFMS[CFMS System]
```
