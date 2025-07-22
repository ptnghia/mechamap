# MechaMap Scripts & Reports

## 📁 Directory Structure

```
scripts/                    # All utility scripts
├── fix-translation-template.php    # Template for translation fixes
├── generate-report-template.php    # Template for report generation
├── check-error-logs.php           # Error log analysis
├── fix-*.php                      # Translation fix scripts
└── test-*.php                     # Testing scripts

docs/reports/               # All reports and documentation
├── PROJECT_ORGANIZATION_REPORT.md  # This organization report
├── FINAL_TRANSLATION_SUMMARY.php  # Translation fix summary
└── *.md                           # Various project reports
```

## 🚀 **Usage Guidelines**

### **Running Scripts:**
```bash
# From project root directory
php scripts/check-error-logs.php
php scripts/fix-auth-modal.php
php scripts/debug-translations.php
```

### **Creating New Scripts:**
1. Copy template: `scripts/fix-translation-template.php`
2. Customize for your needs
3. Save in `scripts/` directory
4. Use naming convention: `fix-{purpose}.php`, `test-{purpose}.php`, `check-{purpose}.php`

### **Generating Reports:**
1. Use template: `scripts/generate-report-template.php`
2. Customize report content
3. Reports auto-save to `docs/reports/`
4. Use naming convention: `PROJECT_STATUS_YYYY_MM_DD.md`

## 📋 **Available Scripts**

### **Translation Management:**
- `fix-translation-template.php` - Template for new translation fixes
- `fix-auth-modal.php` - Fix auth modal translation keys
- `fix-remaining-translations.php` - Comprehensive translation fixer
- `debug-translations.php` - Debug translation issues

### **Error Checking:**
- `check-error-logs.php` - Analyze Laravel error logs
- `check-syntax.php` - Validate PHP syntax
- `test-error.php` - Generate test errors

### **System Utilities:**
- `refresh-all.php` - Clear all caches and refresh system
- `quick-fix.php` - Quick translation fixes

## 📊 **Report Templates**

### **Available Reports:**
- Translation fix summaries
- Project progress reports
- Error analysis reports
- Performance metrics

### **Report Formats:**
- `.md` - Markdown documentation
- `.php` - Executable reports with dynamic content

## 🎯 **Best Practices**

### **For Scripts:**
1. Always create backups before modifications
2. Include progress indicators and error handling
3. Provide clear success/failure messages
4. Document what the script does

### **For Reports:**
1. Include generation timestamp
2. Use consistent formatting
3. Provide actionable recommendations
4. Include metrics and progress tracking

## 🔧 **Development Workflow**

1. **Identify Issue** → Check existing scripts first
2. **Create Script** → Use appropriate template  
3. **Test Script** → Run in development environment
4. **Generate Report** → Document results and findings
5. **Archive** → Move completed items to appropriate directories

---
*MechaMap Project - Scripts & Reports Management*
