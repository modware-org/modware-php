#!/bin/bash

# Migrate Visualizer Page
sqlite3 database.sqlite < scripts/add_visualizer_page.sql
