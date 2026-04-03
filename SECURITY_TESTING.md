# Security Testing Report

**Student ID:** 2439673  
**Project:** MovieWebsiteCollection System  
**Date:** April 2026

---

## Testing Tools Used

I used OWASP ZAP (Zed Attack Proxy) to scan my application for security vulnerabilities. ZAP is a free, open-source security testing tool recommended by OWASP.

**Tool Details:**
- **Name:** OWASP ZAP (Zed Attack Proxy)
- **Version:** 2.14.0
- **Website:** https://www.zaproxy.org/
- **Type:** Automated vulnerability scanner

---

## Testing Process

1. **Setup:**
   - Downloaded and installed OWASP ZAP
   - Configured ZAP to use my local browser
   - Set the target URL to my application

2. **Automated Scan:**
   - Ran automated spider to crawl all pages
   - Performed active scan on all discovered URLs
   - Scanned for common vulnerabilities including:
     - SQL Injection
     - Cross-Site Scripting (XSS)
     - Cross-Site Request Forgery (CSRF)
     - Security misconfigurations
     - Sensitive data exposure

3. **Manual Testing:**
   - Tested login with SQL injection attempts
   - Tried XSS payloads in form fields
   - Checked session management
   - Verified HTTPS usage (where applicable)

---

## Vulnerabilities Found

### High Priority: None ✓

No high-priority vulnerabilities were detected. The prepared statements successfully prevented SQL injection attacks, and Twig's auto-escaping blocked XSS attempts.

### Medium Priority: 1 Issue

**Missing Security Headers:**
- The application doesn't set security headers like X-Frame-Options or Content-Security-Policy
- **Impact:** Low - Could allow clickjacking in some scenarios
- **Mitigation:** These are typically set at the web server level (Apache/Nginx configuration)

### Low Priority: 2 Issues

**1. Session Cookie Settings:**
- Session cookies don't have the HttpOnly flag set by default
- **Impact:** Minimal - Could allow JavaScript access to session cookies
- **Fix Applied:** Added session configuration in config.php

**2. Password Complexity:**
- No minimum password complexity requirements
- **Impact:** Users could choose weak passwords
- **Note:** Current implementation requires minimum 6 characters, which is acceptable for this project

---

## Security Features Verified

### SQL Injection Protection
- **Test:** Attempted SQL injection in login form: `' OR '1'='1`
- **Result:** Failed - Prepared statements prevented injection
- **Status:** PASS

###  XSS Protection
- **Test:** Entered `<script>alert('XSS')</script>` in movie title
- **Result:** Script was escaped and displayed as text
- **Status:** PASS

### Authentication
- **Test:** Tried accessing protected pages without login
- **Result:** Redirected to login page
- **Status:** PASS

### Password Security
- **Test:** Checked database - passwords are hashed with bcrypt
- **Result:** No plain text passwords found
- **Status:** PASS

### CAPTCHA
- **Test:** Attempted registration with wrong CAPTCHA
- **Result:** Registration blocked
- **Status:** PASS

---

## Recommendations

While the application is secure for a student project, here are some improvements for production:

1. **Add CSRF tokens** to all forms
2. **Implement rate limiting** on login attempts
3. **Set security headers** at web server level
4. **Use HTTPS** in production (already available on mi-linux server)
5. **Add password complexity requirements** (uppercase, lowercase, numbers, symbols)
6. **Implement account lockout** after multiple failed login attempts

---

## Conclusion

The security testing revealed no critical vulnerabilities. All five required security features are working correctly:
- Input filtering prevents malicious data
- Output escaping prevents XSS attacks
- Sessions protect sensitive pages
- CAPTCHA prevents automated registration
- Passwords are properly encrypted

The application demonstrates good security practices for a web development project. The few minor issues found are typical for development environments and don't pose significant risks.

