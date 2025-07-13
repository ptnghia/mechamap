# 🛠️ Language Migration Scripts

**Automated tools for MechaMap language file restructuring**

## 📁 **Scripts Overview**

### **1. migrate-language-files.php**
**Purpose**: Migrate keys from oversized messages.php to focused files
**Features**:
- Creates new file structure (nav.php, ui.php, auth.php, etc.)
- Redistributes 600+ keys from messages.php
- Eliminates duplicate keys
- Fixes value inconsistencies

**Usage**:
```bash
# Dry run (preview changes)
php migrate-language-files.php --dry-run --verbose

# Execute migration
php migrate-language-files.php --verbose
```

### **2. update-view-references.php**
**Purpose**: Update 527+ view references from __('messages.*') to new structure
**Features**:
- Batch processing (core files first, then others)
- Pattern matching for dynamic keys
- Comprehensive mapping system
- Progress tracking

**Usage**:
```bash
# Batch 1: Core layout files (200+ references)
php update-view-references.php --batch=1 --verbose

# Batch 2: Remaining files (327+ references)
php update-view-references.php --batch=2 --verbose

# All files at once
php update-view-references.php --verbose
```

### **3. validate-migration.php**
**Purpose**: Validate migration success and detect issues
**Features**:
- File structure validation
- Key consistency checking
- View reference validation
- Duplicate detection
- Performance testing

**Usage**:
```bash
# Basic validation
php validate-migration.php

# Detailed validation
php validate-migration.php --verbose

# Validation with auto-fix
php validate-migration.php --verbose --fix-missing
```

## 🚀 **Migration Workflow**

### **Step 1: Preparation**
```bash
# 1. Create backup (already done in Task 0.1)
# 2. Analyze current state
php validate-migration.php --verbose
```

### **Step 2: Execute Migration**
```bash
# 1. Dry run first
php migrate-language-files.php --dry-run --verbose

# 2. Execute migration
php migrate-language-files.php --verbose

# 3. Validate migration
php validate-migration.php --verbose
```

### **Step 3: Update View References**
```bash
# 1. Update core files first
php update-view-references.php --batch=1 --verbose

# 2. Test core functionality
# 3. Update remaining files
php update-view-references.php --batch=2 --verbose

# 4. Final validation
php validate-migration.php --verbose
```

### **Step 4: Final Validation**
```bash
# 1. Comprehensive validation
php validate-migration.php --verbose

# 2. Performance check
# 3. Language switching test
```

## 📊 **Expected Results**

### **Before Migration**
```
Language Files Structure:
├── messages.php (vi): 623 lines ❌ OVERSIZED
├── messages.php (en): 610 lines ❌ OVERSIZED
├── 50+ duplicate keys ❌ INCONSISTENT
└── 527+ __('messages.*') references ❌ OVERUSED
```

### **After Migration**
```
Language Files Structure:
├── nav.php: ~50 keys (75 lines) ✅ FOCUSED
├── ui.php: ~100 keys (120 lines) ✅ FOCUSED
├── auth.php: ~60 keys (90 lines) ✅ FOCUSED
├── marketplace.php: ~80 keys (110 lines) ✅ FOCUSED
├── forum.php: ~90 keys (130 lines) ✅ FOCUSED
├── common.php: ~70 keys (100 lines) ✅ FOCUSED
├── 0 duplicate keys ✅ CLEAN
└── 527+ updated references ✅ STRUCTURED
```

## ⚠️ **Safety Features**

### **Backup & Rollback**
- Automatic backup creation before migration
- Rollback procedures documented
- Dry-run mode for safe testing

### **Validation & Testing**
- PHP syntax validation
- Key consistency checking
- Performance monitoring
- Missing key detection

### **Error Handling**
- Comprehensive error reporting
- Graceful failure handling
- Detailed logging

## 🔧 **Troubleshooting**

### **Common Issues**

#### **Migration Fails**
```bash
# Check PHP syntax
php -l migrate-language-files.php

# Check permissions
ls -la resources/lang/

# Run with verbose output
php migrate-language-files.php --dry-run --verbose
```

#### **View References Not Updated**
```bash
# Check file permissions
ls -la resources/views/

# Run batch by batch
php update-view-references.php --batch=1 --verbose
php update-view-references.php --batch=2 --verbose

# Validate results
php validate-migration.php --verbose
```

#### **Missing Translation Keys**
```bash
# Run validation
php validate-migration.php --verbose

# Check specific files
grep -r "__('messages\." resources/views/

# Manual fix if needed
```

### **Performance Issues**
```bash
# Check file sizes
find resources/lang -name "*.php" -exec wc -l {} \;

# Test loading time
php validate-migration.php --verbose

# Optimize if needed
```

## 📈 **Success Metrics**

### **File Structure**
- ✅ All files < 150 lines
- ✅ Logical domain separation
- ✅ Zero duplicate keys
- ✅ Consistent naming convention

### **Performance**
- ✅ Loading time < 100ms
- ✅ Memory usage optimized
- ✅ No performance degradation

### **Functionality**
- ✅ Language switching works
- ✅ No missing translations
- ✅ All views render correctly
- ✅ No broken references

---

**🎯 Ready for execution! Run scripts in order for successful migration.**
