#!/usr/bin/env python2
# -*- coding: utf-8 -*-
import sys
import os
import tempfile
import subprocess

def convert_to_silk(input_file, output_file, sample_rate=24000):
    try:
        try:
            import pysilk
            silk_encode = pysilk.encode
        except ImportError:
            print("错误: 未找到pysilk库")
            return False
        
        temp_fd, pcm_file = tempfile.mkstemp(suffix='.pcm')
        os.close(temp_fd)
        
        ffmpeg_cmd = [
            'ffmpeg', '-y', '-i', input_file,
            '-f', 's16le',
            '-ar', str(sample_rate),
            '-ac', '1',
            '-loglevel', 'error',
            pcm_file
        ]
        
        try:
            result = subprocess.check_output(ffmpeg_cmd, stderr=subprocess.STDOUT)
        except subprocess.CalledProcessError as e:
            print("FFmpeg转换失败: " + e.output)
            if os.path.exists(pcm_file):
                os.unlink(pcm_file)
            return False
        except Exception as e:
            print("FFmpeg调用失败: " + str(e))
            if os.path.exists(pcm_file):
                os.unlink(pcm_file)
            return False
        
        if not os.path.exists(pcm_file) or os.path.getsize(pcm_file) < 100:
            print("错误: PCM文件无效")
            if os.path.exists(pcm_file):
                os.unlink(pcm_file)
            return False
        
        try:
            with open(pcm_file, 'rb') as f:
                pcm_data = f.read()
            
            silk_data = silk_encode(pcm_data, sample_rate=sample_rate)
            
            with open(output_file, 'wb') as f:
                f.write(silk_data)
        except Exception as e:
            print("SILK编码失败: " + str(e))
            if os.path.exists(pcm_file):
                os.unlink(pcm_file)
            return False
        
        if os.path.exists(pcm_file):
            os.unlink(pcm_file)
        
        if os.path.exists(output_file) and os.path.getsize(output_file) > 100:
            print("转换成功: {0} -> {1} ({2} bytes)".format(
                input_file, output_file, os.path.getsize(output_file)))
            return True
        else:
            print("错误: 输出文件无效")
            return False
            
    except Exception as e:
        print("转换过程出错: " + str(e))
        if 'pcm_file' in locals() and os.path.exists(pcm_file):
            os.unlink(pcm_file)
        return False

def main():
    if len(sys.argv) < 3:
        print("用法: python convert_to_silk_py2.py <输入文件> <输出文件> [采样率]")
        sys.exit(1)
    
    input_file = sys.argv[1]
    output_file = sys.argv[2]
    sample_rate = int(sys.argv[3]) if len(sys.argv) > 3 else 24000
    
    if not os.path.exists(input_file):
        print("错误: 输入文件不存在: " + input_file)
        sys.exit(1)
    
    success = convert_to_silk(input_file, output_file, sample_rate)
    sys.exit(0 if success else 1)

if __name__ == "__main__":
    main()