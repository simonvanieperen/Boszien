param(
    [ValidateSet('Preview', 'Apply')]
    [string]$Mode = 'Preview'
)

$ErrorActionPreference = 'Stop'
$base = 'https://boszien-control-plane.simon-21d.workers.dev'
$secretPath = 'C:\Users\simon\Downloads\boszien-cloudflare-repair-v1.3\.boszien-action-key.dpapi'
$themeHeader = Join-Path $PSScriptRoot '..\..\..\themes\boszien-base\parts\header.html'

function Get-ActionKey {
    $secure = Get-Content -LiteralPath $secretPath | ConvertTo-SecureString
    $ptr = [Runtime.InteropServices.Marshal]::SecureStringToBSTR($secure)
    try { [Runtime.InteropServices.Marshal]::PtrToStringBSTR($ptr) }
    finally { if ($ptr -ne [IntPtr]::Zero) { [Runtime.InteropServices.Marshal]::ZeroFreeBSTR($ptr) } }
}

function Invoke-ControlPlane {
    param([string]$Endpoint, [object[]]$Items)
    $payload = @{ stopOnError = $true; items = $Items } | ConvertTo-Json -Depth 12 -Compress
    Invoke-RestMethod -Uri "$base/$Endpoint" -Method Post -Headers $headers -ContentType 'application/json' -Body $payload
}

$actionKey = Get-ActionKey
$headers = @{ Authorization = "Bearer $actionKey" }
$headerId = 'boszien-base%2F%2Fheader'
$read = Invoke-ControlPlane -Endpoint 'batch/read' -Items @(
    @{ service = 'frontend'; method = 'GET'; path = '/custom-css' },
    @{ service = 'fse'; method = 'GET'; path = "/template-parts/$headerId" }
)
if (-not $read.ok) { throw 'Readback van Custom CSS of header is mislukt.' }

$currentCss = [string]$read.results[0].data.css
$currentCssHash = [string]$read.results[0].data.contentHash
$currentHeader = [string]$read.results[1].data.item.content.raw
$currentHeaderHash = [string]$read.results[1].data.contentHash
$nextCss = '/* Boszien Base 0.2.1 — noodlaag bewust leeg; styles leven in het versiegebonden theme. */'
$nextHeader = Get-Content -LiteralPath $themeHeader -Raw

$previewItems = @(
    @{
        service = 'frontend'; method = 'POST'; path = '/css/preview-patch'
        body = @{ expectedHash = $currentCssHash; operations = @(@{ type = 'replace_exact'; oldText = $currentCss; newText = $nextCss }) }
    },
    @{
        service = 'fse'; method = 'POST'; path = "/template-parts/$headerId/preview-patch"
        body = @{ expectedHash = $currentHeaderHash; operations = @(@{ type = 'replace_exact'; oldText = $currentHeader; newText = $nextHeader }) }
    }
)
$preview = Invoke-ControlPlane -Endpoint 'batch/execute' -Items $previewItems
if (-not $preview.ok) { $preview | ConvertTo-Json -Depth 12; throw 'Structurele WordPress-preview mislukt; niets geschreven.' }

if ($Mode -eq 'Preview') {
    [pscustomobject]@{
        mode = $Mode
        ok = $true
        objects = @('frontend:custom-css', 'fse:template-part:boszien-base//header')
        previous = @{ css = $currentCssHash; header = $currentHeaderHash }
        preview = @($preview.results | ForEach-Object { $_.data })
    } | ConvertTo-Json -Depth 10
    exit 0
}

$applyItems = @(
    @{
        service = 'frontend'; method = 'POST'; path = '/css/apply-patch'
        body = @{ confirmation = 'APPLY_CSS_PATCH'; expectedHash = $currentCssHash; operations = @(@{ type = 'replace_exact'; oldText = $currentCss; newText = $nextCss }) }
    },
    @{
        service = 'fse'; method = 'POST'; path = "/template-parts/$headerId/apply-patch"
        body = @{ confirmation = 'APPLY_STRUCTURE_PATCH'; expectedHash = $currentHeaderHash; operations = @(@{ type = 'replace_exact'; oldText = $currentHeader; newText = $nextHeader }) }
    }
)
$apply = Invoke-ControlPlane -Endpoint 'batch/execute' -Items $applyItems
if (-not $apply.ok) { $apply | ConvertTo-Json -Depth 12; throw 'WordPress-objectrelease vroegtijdig gestopt.' }

$after = Invoke-ControlPlane -Endpoint 'batch/read' -Items @(
    @{ service = 'frontend'; method = 'GET'; path = '/custom-css' },
    @{ service = 'fse'; method = 'GET'; path = "/template-parts/$headerId" }
)
if (-not $after.ok) { throw 'Readback na WordPress-objectrelease mislukt.' }

[pscustomobject]@{
    mode = $Mode
    ok = $true
    objects = @('frontend:custom-css', 'fse:template-part:boszien-base//header')
    hashes = @{
        css = $after.results[0].data.contentHash
        header = $after.results[1].data.contentHash
    }
    headerStylesheet = if ($after.results[1].data.item.content.raw -match 'ver=0.2.1') { '0.2.1' } else { 'unexpected' }
} | ConvertTo-Json -Depth 8

$actionKey = $null
