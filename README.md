<div align="center">

# ⚡ Enterprise Blog API — Clean Architecture, AI & DevOps

### واجهة برمجية فائقة الأداء مبنية وفق Clean Architecture، ومزودة بمحرك تدقيق ذكي Google Gemini، وتوثيق OpenAPI تفاعلي، ومراقبة حية عبر Laravel Horizon.

[![CI Pipeline](https://img.shields.io/github/actions/workflow/status/AmmarBarraood779/blog-api-DTO/ci.yml?branch=main&style=for-the-badge&logo=githubactions&logoColor=white&label=CI%20Pipeline)](https://github.com/AmmarBarraood779/blog-api-DTO/actions)
[![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Google Gemini](https://img.shields.io/badge/Gemini%20AI-Flash-4285F4?style=for-the-badge&logo=google&logoColor=white)](https://ai.google.dev)
[![Redis](https://img.shields.io/badge/Redis-Queue%20Engine-DC382D?style=for-the-badge&logo=redis&logoColor=white)](https://redis.io)
[![Horizon](https://img.shields.io/badge/Laravel-Horizon-E74430?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com/docs/horizon)
[![API Docs](https://img.shields.io/badge/OpenAPI-Scramble-0EA5E9?style=for-the-badge&logo=openapiinitiative&logoColor=white)](http://localhost:8000/docs/api)
[![Code Style](https://img.shields.io/badge/Pint-PSR--12-success?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com/docs/pint)

<br/>

<p align="center">
  <b>Production-Ready RESTful API</b> صُممت لعزل منطق العمل وتطبيق مبادئ هندسة البرمجيات النظيفة <b>(Clean Architecture)</b> عبر نمطي <b>DTOs</b> و <b>Action Classes</b>، مدعومة بنظام تدقيق محتوى ذكي غير متزامن <b>(Asynchronous AI Moderation)</b> عبر <b>Google Gemini</b> و <b>Redis</b> لضمان استجابة لحظية لا تتجاوز أجزاء من الثانية، ومجهزة بأدوات رصد ومراقبة متقدمة وتوثيق تفاعلي مؤتمت بالكامل.
</p>

---

</div>

## 💎 الركائز الأساسية للمشروع (Core Architecture)

* **Clean Architecture & Thin Controllers:** فصل الاهتمامات عبر كائنات البيانات الثابتة `Readonly DTOs` وفئات الإجراءات الأحادية `Single-Action Classes` مع تفريغ كامل لطبقة التحكم من منطق الأعمال.
* **Asynchronous AI Content Moderation:** استهلاك نموذج **Google Gemini Flash** عبر حزمة `laravel/ai` داخل طوابير خلفية مستقلة (`moderation` queue)؛ يحصل المستخدم على رد `201 Created` فوري في غضون **أقل من 100ms** بينما تتم المعالجة الذكية في الخلفية.
* **Structured AI Decisions:** إلزام الذكاء الاصطناعي بإرجاع استجابة JSON صارمة (`HasStructuredOutput`) لتحديد سلامة المحتوى وحفظ أسباب الرفض آلياً في قاعدة البيانات.
* **Real-time Queue Monitoring (Laravel Horizon):** لوحة تحكم بصرية لمراقبة استهلاك Redis، وزمن استجابة استدعاءات Gemini، وتتبع المهام المكتملة والفاشلة لحظياً.
* **Automated OpenAPI Documentation (Scramble):** توليد توثيق تفاعلي كامل لواجهات الـ API ومخططات الطلبات والاستجابات آلياً من الكود مباشرة بدون كتابة DocBlocks يدوية، مع دعم مصادقة Sanctum Bearer Token.
* **Automated CI/CD Pipeline:** خط أنابيب فحص واختبار سحابي آلي عبر GitHub Actions يشغّل قواعد بيانات MySQL و Redis حية مع كل عملية دفع للكود.

---

## 🏛️ دورة حياة الطلب ومعالجة الـ AI (Architectural Blueprint)

```text
  [ Client Request ]
          │
          ▼
┌─────────────────────────┐
│   StoreCommentRequest   │  ──► التحقق من صحة المدخلات وقواعد البيانات (Validation)
└──────────┬──────────────┘
           │  $request->toDTO($post->id)
           ▼
┌─────────────────────────┐
│    CreateCommentDTO     │  ──► كائن غير قابل للتعديل يضمن أمان الأنواع (Strict Types)
└──────────┬──────────────┘
           │  execute($user, $dto)
           ▼
┌─────────────────────────┐
│    AddCommentAction     │  ──► تنفيذ منطق حفظ التعليق الأولي (status: pending)
└─────┬──────────────┬────┘
      │              │
      │              ▼
      │      ┌─────────────────────────┐
      │      │   ModerateCommentJob    │  ──► الإرسال لطابور Redis المخصص ('moderation')
      │      └──────────┬──────────────┘
      │                 │
      │                 ▼
      │      ┌─────────────────────────┐
      │      │ CommentModerator Agent  │  ──► Google Gemini Flash (فحص لغوي وأخلاقي)
      │      └──────────┬──────────────┘
      │                 │
      │                 ▼
      │      ┌─────────────────────────┐
      │      │    Database Update      │  ──► تحويل الحالة إلى approved أو rejected
      │      └─────────────────────────┘
      ▼
┌─────────────────────────┐
│     CommentResource     │  ──► تغليف الاستجابة وفق معيار JSON API
└──────────┬──────────────┘
           │
           ▼
     [ HTTP 201 ] (استجابة فورية للعميل دون انتظار معالجة الذكاء الاصطناعي)