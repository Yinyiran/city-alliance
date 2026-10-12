param(
    [string]$msg = ""
)

$RootDir = $PSScriptRoot
$GitDir = Join-Path $RootDir ".git_server"
$WorkTree = Join-Path $RootDir "server"

Write-Host "==========================================================" -ForegroundColor Cyan
Write-Host " [RUN] 正在同步并推送 server 服务端代码到独立仓库..." -ForegroundColor Cyan
Write-Host " 目标仓库: https://github.com/Yinyiran/city-alliance-server.git" -ForegroundColor Cyan
Write-Host "==========================================================" -ForegroundColor Cyan

# 1. 暂存所有变更
git --git-dir="$GitDir" --work-tree="$WorkTree" add -A

# 2. 检查是否有变动
$diff = git --git-dir="$GitDir" --work-tree="$WorkTree" diff --cached --name-only
if (-not $diff) {
    Write-Host "`n[提示] server 目录没有检测到任何新代码改动。" -ForegroundColor Yellow
} else {
    Write-Host "`n[变更] 检测到以下变动文件:" -ForegroundColor Green
    $diff | Select-Object -First 20 | ForEach-Object { Write-Host "   $_" -ForegroundColor Gray }
    if ($diff.Count -gt 20) {
        Write-Host "   ... 等共 $($diff.Count) 个文件" -ForegroundColor Gray
    }

    if ([string]::IsNullOrWhiteSpace($msg)) {
        $msg = Read-Host "`n请输入提交信息 (直接回车默认: update server)"
    }
    if ([string]::IsNullOrWhiteSpace($msg)) {
        $msg = "update server"
    }

    git --git-dir="$GitDir" --work-tree="$WorkTree" commit -m "$msg"
    Write-Host "[成功] 本地提交完成: $msg" -ForegroundColor Green
}

# 3. 推送到远程
Write-Host "`n[推送] 正在推送到 GitHub (city-alliance-server:main)..." -ForegroundColor Cyan
git --git-dir="$GitDir" --work-tree="$WorkTree" push origin main

if ($LASTEXITCODE -eq 0) {
    Write-Host "`n==========================================================" -ForegroundColor Green
    Write-Host " [完成] server 代码已成功同步到远程仓库！" -ForegroundColor Green
    Write-Host " 服务器上现在可以执行: git pull 正常更新了！" -ForegroundColor Green
    Write-Host "==========================================================" -ForegroundColor Green
} else {
    Write-Host "`n[失败] 推送遇到问题，请检查网络或报错信息。" -ForegroundColor Red
}
