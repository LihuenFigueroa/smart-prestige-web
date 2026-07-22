require('dotenv').config();
const express    = require('express');
const cors       = require('cors');
const nodemailer = require('nodemailer');
const path       = require('path');

const app  = express();
const PORT = process.env.PORT || 3001;

// CORS — permite que el sitio en cualquier puerto local llame a este servidor
app.use(cors());

// Parsear body JSON y form
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// ── Endpoint: recibir formulario ─────────────────────────────────────────
app.post('/enviar', async (req, res) => {
  const { nombre, apellido, ciudad, email, celular, concesionario, modelo, consulta } = req.body;

  const transporter = nodemailer.createTransport({
    service: 'gmail',
    auth: {
      user: process.env.MAIL_USER,
      pass: process.env.MAIL_PASS,
    },
  });

  const mailOptions = {
    from: `"smart Argentina — Formulario" <${process.env.MAIL_USER}>`,
    to:   process.env.MAIL_TO,
    subject: `Nueva consulta — ${nombre || ''} ${apellido || ''}`,
    text: [
      `Nombre:         ${nombre || '—'}`,
      `Apellido:       ${apellido || '—'}`,
      `Ciudad:         ${ciudad || '—'}`,
      `Email:          ${email || '—'}`,
      `Celular:        ${celular || '—'}`,
      `Concesionario:  ${concesionario || '—'}`,
      `Modelo:         ${modelo || '—'}`,
      ``,
      `Consulta:`,
      consulta || '—',
    ].join('\n'),
    html: `<!DOCTYPE html>
<html lang="es">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;padding:0;background-color:#f0efe9;font-family:'Helvetica Neue',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f0efe9;padding:40px 16px;">
  <tr><td align="center">
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background:#ffffff;">

      <!-- Header -->
      <tr>
        <td style="background:#141413;padding:24px 36px 22px;">
          <p style="margin:0;color:#ffffff;font-size:11px;letter-spacing:0.18em;text-transform:uppercase;">smart Argentina</p>
        </td>
      </tr>

      <!-- Título -->
      <tr>
        <td style="padding:36px 36px 20px;">
          <p style="margin:0 0 10px;color:#9ca3af;font-size:10px;letter-spacing:0.12em;text-transform:uppercase;">Nueva consulta recibida</p>
          <h1 style="margin:0;color:#141413;font-size:26px;font-weight:400;line-height:1.1;letter-spacing:-0.02em;">${nombre || ''} ${apellido || ''}</h1>
        </td>
      </tr>

      <!-- Divider -->
      <tr><td style="padding:0 36px;"><div style="border-top:1px solid #e5e7eb;"></div></td></tr>

      <!-- Campos -->
      <tr>
        <td style="padding:0 36px;">
          <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
              <td style="padding:14px 0;border-bottom:1px solid #f3f4f6;width:110px;vertical-align:top;">
                <span style="color:#9ca3af;font-size:10px;letter-spacing:0.1em;text-transform:uppercase;">Ciudad</span>
              </td>
              <td style="padding:14px 0 14px 20px;border-bottom:1px solid #f3f4f6;vertical-align:top;">
                <span style="color:#141413;font-size:13px;">${ciudad || '—'}</span>
              </td>
            </tr>
            <tr>
              <td style="padding:14px 0;border-bottom:1px solid #f3f4f6;vertical-align:top;">
                <span style="color:#9ca3af;font-size:10px;letter-spacing:0.1em;text-transform:uppercase;">Email</span>
              </td>
              <td style="padding:14px 0 14px 20px;border-bottom:1px solid #f3f4f6;vertical-align:top;">
                <span style="color:#141413;font-size:13px;">${email || '—'}</span>
              </td>
            </tr>
            <tr>
              <td style="padding:14px 0;border-bottom:1px solid #f3f4f6;vertical-align:top;">
                <span style="color:#9ca3af;font-size:10px;letter-spacing:0.1em;text-transform:uppercase;">Celular</span>
              </td>
              <td style="padding:14px 0 14px 20px;border-bottom:1px solid #f3f4f6;vertical-align:top;">
                <span style="color:#141413;font-size:13px;">${celular || '—'}</span>
              </td>
            </tr>
            <tr>
              <td style="padding:14px 0;border-bottom:1px solid #f3f4f6;vertical-align:top;">
                <span style="color:#9ca3af;font-size:10px;letter-spacing:0.1em;text-transform:uppercase;">Concesionario</span>
              </td>
              <td style="padding:14px 0 14px 20px;border-bottom:1px solid #f3f4f6;vertical-align:top;">
                <span style="color:#141413;font-size:13px;">${concesionario || '—'}</span>
              </td>
            </tr>
            <tr>
              <td style="padding:14px 0;border-bottom:1px solid #f3f4f6;vertical-align:top;">
                <span style="color:#9ca3af;font-size:10px;letter-spacing:0.1em;text-transform:uppercase;">Modelo</span>
              </td>
              <td style="padding:14px 0 14px 20px;border-bottom:1px solid #f3f4f6;vertical-align:top;">
                <span style="color:#141413;font-size:13px;">${modelo || '—'}</span>
              </td>
            </tr>
          </table>
        </td>
      </tr>

      <!-- Consulta -->
      <tr>
        <td style="padding:24px 36px 32px;">
          <p style="margin:0 0 10px;color:#9ca3af;font-size:10px;letter-spacing:0.12em;text-transform:uppercase;">Consulta</p>
          <p style="margin:0;color:#141413;font-size:13px;line-height:1.7;">${(consulta || '—').replace(/\n/g, '<br>')}</p>
        </td>
      </tr>

      <!-- Footer -->
      <tr>
        <td style="background:#141413;padding:18px 36px;">
          <p style="margin:0;color:#ffffff;opacity:0.35;font-size:11px;">© 2026 smart Argentina</p>
        </td>
      </tr>

    </table>
  </td></tr>
</table>
</body>
</html>`,
  };

  try {
    await transporter.sendMail(mailOptions);
    res.json({ ok: true });
  } catch (err) {
    console.error('Error enviando mail:', err);
    res.status(500).json({ ok: false, error: err.message });
  }
});

app.listen(PORT, () => {
  console.log(`Servidor corriendo en http://localhost:${PORT}`);
});
