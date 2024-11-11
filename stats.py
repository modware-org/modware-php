import os
from collections import defaultdict
import json
from pathlib import Path

class ProjectAnalyzer:
    def __init__(self, root_dir="."):
        self.root_dir = root_dir
        self.stats = {
            "total_files": 0,
            "total_lines": 0,
            "files_by_extension": defaultdict(int),
            "lines_by_extension": defaultdict(int),
            "directory_structure": {},
            "files_by_directory": defaultdict(int),
            "lines_by_directory": defaultdict(int)
        }

    def count_lines(self, file_path):
        try:
            with open(file_path, 'r', encoding='utf-8') as f:
                return sum(1 for line in f if line.strip())
        except Exception:
            return 0

    def get_file_extension(self, file_path):
        return os.path.splitext(file_path)[1].lower() or 'no_extension'

    def analyze_directory(self, current_dir=None):
        if current_dir is None:
            current_dir = self.root_dir

        structure = {"files": [], "directories": {}}

        try:
            for item in os.listdir(current_dir):
                full_path = os.path.join(current_dir, item)

                if os.path.isfile(full_path):
                    # Skip hidden files and certain directories
                    if item.startswith('.') or '__pycache__' in full_path:
                        continue

                    self.stats["total_files"] += 1
                    extension = self.get_file_extension(item)
                    lines = self.count_lines(full_path)

                    self.stats["total_lines"] += lines
                    self.stats["files_by_extension"][extension] += 1
                    self.stats["lines_by_extension"][extension] += lines
                    self.stats["files_by_directory"][current_dir] += 1
                    self.stats["lines_by_directory"][current_dir] += lines

                    structure["files"].append({
                        "name": item,
                        "extension": extension,
                        "lines": lines
                    })

                elif os.path.isdir(full_path):
                    # Skip certain directories
                    if item in ['.git', 'node_modules', '__pycache__', 'venv', '.idea']:
                        continue

                    structure["directories"][item] = self.analyze_directory(full_path)

        except PermissionError:
            return structure

        return structure

    def analyze(self):
        self.stats["directory_structure"] = self.analyze_directory()
        return self.stats

    def generate_report(self):
        report = [
            "📊 Project Statistics Report 📊",
            "=" * 40,
            f"\n📁 Total Files: {self.stats['total_files']}",
            f"📝 Total Lines of Code: {self.stats['total_lines']}",
            "\n📑 Files by Extension:",
            "=" * 20
        ]

        for ext, count in sorted(self.stats['files_by_extension'].items()):
            lines = self.stats['lines_by_extension'][ext]
            report.append(f"{ext}: {count} files ({lines} lines)")

        report.extend([
            "\n📂 Files by Directory:",
            "=" * 20
        ])

        for directory, count in sorted(self.stats['files_by_directory'].items()):
            lines = self.stats['lines_by_directory'][directory]
            rel_path = os.path.relpath(directory, self.root_dir)
            report.append(f"{rel_path}: {count} files ({lines} lines)")

        return "\n".join(report)

    def save_report(self, output_dir="stats"):
        # Create output directory if it doesn't exist
        os.makedirs(output_dir, exist_ok=True)

        # Save detailed JSON report
        with open(os.path.join(output_dir, "detailed_stats.json"), 'w') as f:
            json.dump(self.stats, f, indent=2, default=str)

        # Save text report
        with open(os.path.join(output_dir, "report.txt"), 'w') as f:
            f.write(self.generate_report())

def main():
    # Get project directory from user or use current directory
    project_dir = input("Enter project directory path (or press Enter for current directory): ").strip()
    if not project_dir:
        project_dir = "."

    # Create analyzer and run analysis
    analyzer = ProjectAnalyzer(project_dir)
    analyzer.analyze()

    # Print report to console
    print("\n" + analyzer.generate_report())

    # Save reports to files
    analyzer.save_report()
    print("\nDetailed reports have been saved to the 'stats' directory.")

if __name__ == "__main__":
    main()