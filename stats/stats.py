import os
from collections import defaultdict
import json
from pathlib import Path

class ProjectAnalyzer:
    # Default folders to exclude
    DEFAULT_EXCLUDE = {
        '.git',
        'node_modules',
        '__pycache__',
        'venv',
        '.venv',
        '.idea',
        '.vs',
        '.vscode',
        'dist',
        'build',
        'target',
        'bin',
        'obj',
        'packages',
        'vendor',
        'bower_components',
        'cache',
        '.next',
        '.nuxt',
        '.output',
        'coverage',
        '.gradle',
        '.mvn',
        '.pytest_cache',
        '.mypy_cache',
        'migrations',
        'logs'
    }

    def __init__(self, root_dir=".", exclude_folders=None, exclude_extensions=None):
        self.root_dir = root_dir
        # Combine default and user-specified folders to exclude
        self.exclude_folders = self.DEFAULT_EXCLUDE | (set(exclude_folders) if exclude_folders else set())
        # Default extensions to exclude
        self.exclude_extensions = set(exclude_extensions) if exclude_extensions else {
            '.pyc', '.pyo', '.pyd', '.dll', '.so', '.dylib',
            '.log', '.lock', '.tmp', '.temp',
            '.png', '.jpg', '.jpeg', '.gif', '.ico', '.svg',
            '.mp3', '.mp4', '.wav', '.avi', '.mov',
            '.zip', '.tar', '.gz', '.rar', '.7z'
        }
        self.stats = {
            "total_files": 0,
            "total_lines": 0,
            "files_by_extension": defaultdict(int),
            "lines_by_extension": defaultdict(int),
            "directory_structure": {},
            "files_by_directory": defaultdict(int),
            "lines_by_directory": defaultdict(int),
            "config": {
                "excluded_folders": sorted(list(self.exclude_folders)),
                "excluded_extensions": sorted(list(self.exclude_extensions))
            }
        }

    def should_exclude_path(self, path):
        """Check if a path should be excluded based on configured rules"""
        path_parts = Path(path).parts
        return any(excluded in path_parts for excluded in self.exclude_folders)

    def should_exclude_file(self, filename):
        """Check if a file should be excluded based on its extension"""
        return any(filename.endswith(ext) for ext in self.exclude_extensions)

    def count_lines(self, file_path):
        try:
            with open(file_path, 'r', encoding='utf-8') as f:
                return sum(1 for line in f if line.strip())
        except (UnicodeDecodeError, PermissionError, FileNotFoundError):
            try:
                # Try with a different encoding if UTF-8 fails
                with open(file_path, 'r', encoding='latin-1') as f:
                    return sum(1 for line in f if line.strip())
            except:
                return 0

    def get_file_extension(self, file_path):
        return os.path.splitext(file_path)[1].lower() or 'no_extension'

    def analyze_directory(self, current_dir=None):
        if current_dir is None:
            current_dir = self.root_dir

        if self.should_exclude_path(current_dir):
            return None

        structure = {"files": [], "directories": {}}

        try:
            for item in os.listdir(current_dir):
                if item.startswith('.'):  # Skip hidden files/folders
                    continue

                full_path = os.path.join(current_dir, item)

                if os.path.isfile(full_path):
                    if self.should_exclude_file(item):
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
                    subdir_structure = self.analyze_directory(full_path)
                    if subdir_structure is not None:  # Only add if not excluded
                        structure["directories"][item] = subdir_structure

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

        # Sort extensions by number of files
        sorted_extensions = sorted(
            self.stats['files_by_extension'].items(),
            key=lambda x: (-x[1], x[0])  # Sort by count (descending) then name
        )

        for ext, count in sorted_extensions:
            lines = self.stats['lines_by_extension'][ext]
            report.append(f"{ext}: {count} files ({lines:,} lines)")

        report.extend([
            "\n📂 Files by Directory:",
            "=" * 20
        ])

        # Sort directories by number of files
        sorted_directories = sorted(
            self.stats['files_by_directory'].items(),
            key=lambda x: (-x[1], x[0])  # Sort by count (descending) then path
        )

        for directory, count in sorted_directories:
            lines = self.stats['lines_by_directory'][directory]
            rel_path = os.path.relpath(directory, self.root_dir)
            report.append(f"{rel_path}: {count} files ({lines:,} lines)")

        report.extend([
                          "\n🚫 Excluded Folders:",
                          "=" * 20
                      ] + sorted(list(self.exclude_folders)))

        report.extend([
                          "\n🚫 Excluded Extensions:",
                          "=" * 20
                      ] + sorted(list(self.exclude_extensions)))

        return "\n".join(report)

    def save_report(self, output_dir="."):
        os.makedirs(output_dir, exist_ok=True)

        # Save detailed JSON report
        with open(os.path.join(output_dir, "stats.json"), 'w') as f:
            json.dump(self.stats, f, indent=2, default=str)

        # Save text report
        with open(os.path.join(output_dir, "report.txt"), 'w') as f:
            f.write(self.generate_report())

def main():
    # Get project directory from user
    project_dir = input("Enter project directory path (or press Enter for current directory): ").strip()
    if not project_dir:
        project_dir = ".."

    # Get additional folders to exclude
    print("\nEnter additional folders to exclude (one per line, empty line to finish):")
    additional_exclude = set()
    while True:
        folder = input().strip()
        if not folder:
            break
        additional_exclude.add(folder)

    # Create analyzer and run analysis
    analyzer = ProjectAnalyzer(project_dir, exclude_folders=additional_exclude)
    analyzer.analyze()

    # Print report to console
    print("\n" + analyzer.generate_report())

    # Save reports to files
    analyzer.save_report()
    print("\nDetailed reports have been saved to the 'stats' directory.")

if __name__ == "__main__":
    main()