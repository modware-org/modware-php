#!/bin/bash
sqlite3 database.sqlite < scripts/migrate_section_translations.sql
