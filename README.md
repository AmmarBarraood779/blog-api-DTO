<div align="center">

# ⚡ Blog API — Clean Architecture & AI Moderation

### واجهة برمجية فائقة الأداء مبنية وفق معمارية برمجية متقدمة وتدقيق آلي ذكي

[![CI Pipeline](https://img.shields.io/github/actions/workflow/status/AmmarBarraood779/blog-api-DTO/ci.yml?branch=main&style=for-the-badge&logo=githubactions&logoColor=white&label=CI%20Pipeline)](https://github.com/AmmarBarraood779/blog-api-DTO/actions)
[![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Google Gemini](https://img.shields.io/badge/Gemini%20AI-Flash-4285F4?style=for-the-badge&logo=google&logoColor=white)](https://ai.google.dev)
[![Redis](https://img.shields.io/badge/Redis-Queue%20Engine-DC382D?style=for-the-badge&logo=redis&logoColor=white)](https://redis.io)
[![Code Style](https://img.shields.io/badge/Pint-PSR--12-success?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com/docs/pint)

<br/>

<p align="center">
  <b>Enterprise-Grade RESTful API</b> صُممت لعزل منطق العمل وتطبيق مبادئ هندسة البرمجيات النظيفة <b>(Clean Architecture)</b> عبر نمطي <b>DTOs</b> و <b>Actions</b>، مدعومة بمحرك تدقيق ذكي للمحتوى غير متزامن <b>(Asynchronous AI Moderation)</b> عبر <b>Google Gemini</b> و <b>Redis</b> لضمان سرعة استجابة لا تتجاوز أجزاء من الثانية.
</p>

---

</div>

## 💎 أبرز ما يميز النظام (Core Highlights)

* **Clean Architecture & Separation of Concerns:** التخلص التام من منطق الأعمال داخل الـ Controllers وتحويلها إلى `Thin Controllers` عبر نمطي DTO و Action Classes.
* **Asynchronous AI Pipeline (Zero-Latency Impact):** عزل فحص المحتوى بالذكاء الاصطناعي (الذي يستغرق 2-3 ثوانٍ) في طابور خلفي مستقل على Redis؛ يحصل العميل على رد `201 Created` خلال **أقل من 100ms**.
* **Structured AI Response:** إجبار نموذج Gemini على إنتاج مخرجات JSON محددة ومطابقة لنمط `HasStructuredOutput` لضمان دقة اتخاذ القرار آلياً (`approved` أو `rejected`).
* **CI/CD Automation:** خط أنابيب آلي عبر GitHub Actions يختبر الكود، ويفحص التنسيق بـ Pint، ويشغّل خدمات MySQL و Redis مع كل عملية دفع أو دمج.

---

## 🏛️ دورة حياة الطلب والنمط المعماري (Architectural Flow)

تم تصميم تدفق البيانات وفق خط سير أحادي الاتجاه يمنع الاعتماد المتبادل ويحقق أقصى درجات الأمان النوعي (**Type Safety**):

```text
  [ Client Request ]
          │
          ▼
┌─────────────────────────┐
│   StoreCommentRequest   │  ──► Validation Rules & Authorization
└──────────┬──────────────┘
           │  $request->toDTO($post->id)
           ▼
┌─────────────────────────┐
│    CreateCommentDTO     │  ──► Readonly / Immutable / Strict Types
└──────────┬──────────────┘
           │  execute($user, $dto)
           ▼
┌─────────────────────────┐
│    AddCommentAction     │  ──► Single Responsibility Business Logic
└─────┬──────────────┬────┘
      │              │
      │              ▼
      │      ┌─────────────────────────┐
      │      │   ModerateCommentJob    │  ──► Dispatched to Redis ('moderation' queue)
      │      └──────────┬──────────────┘
      │                 │
      │                 ▼
      │      ┌─────────────────────────┐
      │      │ CommentModerator Agent  │  ──► Google Gemini Flash (Structured JSON)
      │      └──────────┬──────────────┘
      │                 │
      │                 ▼
      │      ┌─────────────────────────┐
      │      │    Database Update      │  ──► status: approved | rejected + reason
      │      └─────────────────────────┘
      ▼
┌─────────────────────────┐
│     CommentResource     │  ──► JSON Formatting & Entity Transformation
└──────────┬──────────────┘
           │
           ▼
     [ HTTP 201 ] (Immediate Response to Client)